<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExportRequest;
use App\Services\ExportService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Serves data exports to token-authenticated API clients.
 */
class ExportController extends Controller
{
    /**
     * Generates the requested export and streams it back.
     */
    public function __invoke(ExportRequest $request, ExportService $exportService): BinaryFileResponse
    {
        $file = $exportService->export($request->validated());

        return response()->download(
            $file->getFilename(),
            $file->getSuggestedFilename(),
            ['Content-Type' => $file->getContentType()]
        )->deleteFileAfterSend();
    }
}
