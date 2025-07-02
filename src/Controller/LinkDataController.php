<?php

namespace App\Controller;

use App\Repository\LinkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

final class LinkDataController extends AbstractController
{
    public function getLinkForm(): Response
    {
        return $this->render('link_data/create-form.html.twig', [
            'shortUrl' => null,
            'error' => null,
        ]);
    }

    public function postLink(Request $request, LinkRepository $linkRepository): Response
    {
        $originalUrl = $request->request->get('original-url');
        $link = null;

        if (!filter_var($originalUrl, FILTER_VALIDATE_URL)) {
            $error = 'Введите корректную ссылку';
        } else {
            $link = $linkRepository->addLink($originalUrl);
        }

        return $this->render('link_data/create-form.html.twig', [
            'shortUrl' => $link?->getFullShortUrl(),
            'error' => $error ?? null,
        ]);
    }

    public function getLink(string $slug, LinkRepository $linkRepository): Response
    {
        $link = $linkRepository->findOneBy(['shortUrl' => $slug]);

        if (!$link or $link->isDeleted())
            return new Response(
                $this->renderView('link_data/error.html.twig', [
                    'error' => 'Данная ссылка никуда не ведет',
                ]),
                Response::HTTP_NOT_FOUND
            );

        $linkRepository->addLinkClick($link);
        return $this->redirect($link->getOriginalUrl());
    }

    public function getLinksList(LinkRepository $linkRepository): Response
    {
        $links = $linkRepository->findAllByIsDeleted(false);
        return $this->render('link_data/list.html.twig', [
            'links' => $links,
        ]);
    }

    private function getSelectedLinks(Request $request): ?array
    {
        $linksIds = $request->request->all('selected_links');

        foreach ($linksIds as &$linkId) {
            if (filter_var($linkId, FILTER_VALIDATE_INT))
                $linkId = (int)$linkId;
            else
                return null;
        }

        return $linksIds;
    }

    public function deleteLinks(Request $request, LinkRepository $linkRepository): Response
    {
        $linksIds = $this->getSelectedLinks($request);

        if (!$linksIds)
            return new Response(
                $this->renderView('link_data/error.html.twig', [
                    'error' => 'В веденных данных содержится ошибка',
                ]),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

        $linkRepository->setDeletedById($linksIds, true);

        return $this->redirectToRoute('getLinksList');
    }

    public function getDeletedLinksList(LinkRepository $linkRepository): Response
    {
        $linkRepository->deleteOldLinks();

        $links = $linkRepository->findAllByIsDeleted(true);
        return $this->render('link_data/deleted-list.html.twig', [
            'links' => $links,
        ]);
    }

    public function recoverLinks(Request $request, LinkRepository $linkRepository): Response
    {
        $linksIds = $this->getSelectedLinks($request);

        if (!$linksIds)
            return new Response(
                $this->renderView('link_data/error.html.twig', [
                    'error' => 'В веденных данных содержится ошибка',
                ]),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

        $linkRepository->setDeletedById($linksIds, false);

        return $this->redirectToRoute('getDeletedLinksList');
    }
}
