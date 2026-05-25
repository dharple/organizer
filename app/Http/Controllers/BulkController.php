<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Http\Controllers;

use App\Http\Requests\ExportRequest;
use App\Services\ExportService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * Handles bulk data export operations.
 */
class BulkController extends Controller
{
    /**
     * Displays the data export form.
     */
    public function export(): View
    {
        return view('bulk.export');
    }

    /**
     * Processes the export form submission and streams the file download.
     */
    public function exportSubmit(ExportRequest $request, ExportService $exportService): View|Response|RedirectResponse
    {
        try {
            $file = $exportService->export($request->validated());

            return response()->download(
                $file->getFilename(),
                $file->getSuggestedFilename(),
                ['Content-Type' => $file->getContentType()]
            )->deleteFileAfterSend();
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
