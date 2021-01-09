<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Form\ExportType;
use App\Service\ExportService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Supports bulk opertions: exporting and importing from other formats
 */
class BulkController extends AbstractController
{
    /**
     * @Route("/export", name="app_export")
     */
    public function export(Request $request, ExportService $exportService)
    {
        $form = $this->createForm(ExportType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // do the export
            try {
                $file = $exportService->export($form->getData());
                $this->addFlash('success', 'Export complete');
                $response = $this->file($file->getFilename(), $file->getSuggestedFilename());
                $response->headers->set('Content-Type', $file->getContentType());
                $response->deleteFileAfterSend();
                return $response;
            } catch (Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render(
            'bulk/export.html.twig',
            [
                'form'    => $form->createView(),
            ]
        );
    }
}
