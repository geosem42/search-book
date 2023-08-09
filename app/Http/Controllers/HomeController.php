<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\File;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
	public function index()
	{
		return Inertia::render('Home');
	}

	public function documents()
	{
		$documents = File::where('user_id', Auth::id())->orderBy('created_at', 'DESC')->get();

		return response()->json(['documents' => $documents]);
	}

	public function upload(Request $request)
	{
		try {
			$request->validate([
				'file' => 'required|file|mimes:pdf',
			]);

			// Generate a unique name for the file
			$name = Str::uuid() . '.pdf';

			// Get the original name of the uploaded file
			$originalName = $request->file('file')->getClientOriginalName();

			// Store the uploaded file
			$path = $request->file('file')->storeAs('public', $name);

			// Extract text from PDF
			$parser = new Parser();
			$pdf = $parser->parseFile(storage_path('app/' . $path));

			// Initialize an array to store the text from each page
			$pages = [];

			// Loop over each page of the PDF file
			foreach ($pdf->getPages() as $pageNumber => $page) {
				// Extract the text from the page
				$text = $page->getText();

				// Convert text to UTF-8
				$text = mb_convert_encoding($text, 'UTF-8');

				// Add the page number and text to the array
				$pages[] = [
					'page' => $pageNumber + 1,
					'text' => $text,
				];
			}

			// Encode the pages array as a JSON string
			$text = json_encode($pages);

			// Save record in database
			File::create([
				'name' => $name,
				'original_name' => $originalName,
				'text' => $text,
				'user_id' => Auth::id(),
			]);

			return response()->json(['success' => true, 'message' => 'File has been uploaded.']);
		} catch (\Illuminate\Validation\ValidationException $e) {
			return response()->json(['success' => false, 'error' => 'Only PDF files are allowed.']);
		} catch (\Exception $e) {
			Log::error($e->getMessage());
			return response()->json(['success' => false, 'error' => $e->getMessage()]);
		}
	}

	public function search(Request $request)
	{
		// Get the search query
		$query = $request->input('query');

		// Escape special characters in the search query
		$query = preg_quote($query, '/');

		// Allow for spaces between words in the search query
		$query = str_replace(' ', '\s+', $query);

		// Get the selected document ID
		$documentId = $request->input('document');

		// Initialize results array
		$results = [];

		// Check if a search query was provided
		if ($query && $documentId) {
			// Find the selected document in the database
			$document = File::find($documentId);

			// Check if the document was found
			if ($document) {
				// Decode the JSON-encoded text from the document
				$pages = json_decode($document->text, true);

				// Loop over each page of the document
				foreach ($pages as $page) {
					// Get the page number and text content
					$pageNumber = $page['page'];
					$text = $page['text'];
					$text = mb_convert_encoding($text, 'UTF-8');

					// Search for matches in this page
					preg_match_all("/(\S*(?:\s\S*){0,5})$query(\S*(?:\s\S*){0,5})/i", $text, $matches, PREG_SET_ORDER);

					// Process results
					foreach ($matches as $match) {
						// Get snippet
						$snippet = $match[1] . $query . $match[2];

						// Replace \s+ with a normal space
						$snippet = preg_replace('/\\\s\+/', ' ', $snippet);

						// Add result
						$results[] = [
							'page' => (int)$pageNumber,
							'snippet' => trim($snippet),
						];
					}
				}
			}
		}

		// Return a JSON response with results
		return response()->json(['results' => array_values(array_unique($results, SORT_REGULAR))]);
	}
}
