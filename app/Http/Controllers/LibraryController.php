<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\BookBorrow;
use App\Models\User;

class LibraryController extends Controller
{
    /**
     * Display a listing of library books.
     */
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $like = \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $query->where(fn($q) => $q->where('title', $like, "%{$search}%")
                                      ->orWhere('author', $like, "%{$search}%")
                                      ->orWhere('isbn', $like, "%{$search}%")
                                      ->orWhere('category', $like, "%{$search}%"));
        }

        $books = $query->paginate(10);
        return view('library.index', compact('books'));
    }

    /**
     * Show form to create/catalog a book.
     */
    public function create()
    {
        return view('library.create');
    }

    /**
     * Store new book copy in the catalog.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'unique:books,isbn'],
            'category' => ['nullable', 'string', 'max:255'],
            'total_copies' => ['required', 'integer', 'min:1'],
            'location_shelf' => ['nullable', 'string', 'max:100'],
        ]);

        Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'category' => $request->category,
            'total_copies' => $request->total_copies,
            'available_copies' => $request->total_copies, // initially same
            'location_shelf' => $request->location_shelf,
        ]);

        return redirect()->route('library.index')->with('success', 'Book added to library catalog.');
    }

    /**
     * Display current active borrows.
     */
    public function borrowsIndex()
    {
        $borrows = BookBorrow::with(['book', 'user'])->orderBy('due_date')->paginate(10);
        return view('library.borrows', compact('borrows'));
    }

    /**
     * Show form to borrow a book.
     */
    public function borrowCreate()
    {
        $books = Book::where('available_copies', '>', 0)->get();
        $users = User::all();
        return view('library.borrow-create', compact('books', 'users'));
    }

    /**
     * Process borrowing transaction.
     */
    public function borrowStore(Request $request)
    {
        $request->validate([
            'book_id' => ['required', 'exists:books,id'],
            'user_id' => ['required', 'exists:users,id'],
            'due_date' => ['required', 'date', 'after:today'],
        ]);

        $book = Book::findOrFail($request->book_id);

        if ($book->available_copies <= 0) {
            return back()->withErrors(['book_id' => 'This book has no available copies remaining.']);
        }

        // Create transaction
        BookBorrow::create([
            'book_id' => $book->id,
            'user_id' => $request->user_id,
            'borrow_date' => date('Y-m-d'),
            'due_date' => $request->due_date,
            'status' => 'borrowed',
        ]);

        // Decrement available copies
        $book->decrement('available_copies');

        return redirect()->route('library.borrows.index')->with('success', 'Book borrowing registered.');
    }

    /**
     * Process returning transaction.
     */
    public function returnBook(BookBorrow $borrow)
    {
        if ($borrow->status === 'returned') {
            return back()->with('error', 'This book has already been returned.');
        }

        $today = date('Y-m-d');
        $dueDate = $borrow->due_date->format('Y-m-d');
        $fine = 0.00;

        // Calculate fine if overdue (e.g. $1.00 per day)
        if ($today > $dueDate) {
            $daysDiff = (strtotime($today) - strtotime($dueDate)) / (60 * 60 * 24);
            $fine = $daysDiff * 1.00; // $1.00 per day fine
        }

        $borrow->update([
            'return_date' => $today,
            'fine_amount' => $fine,
            'status' => 'returned',
        ]);

        // Increment available copies
        $borrow->book->increment('available_copies');

        return redirect()->route('library.borrows.index')
            ->with('success', 'Book returned successfully.' . ($fine > 0 ? " Overdue fine: \$" . number_format($fine, 2) : ""));
    }
}
