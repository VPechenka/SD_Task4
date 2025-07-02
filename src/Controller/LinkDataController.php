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

        if (!filter_var($originalUrl, FILTER_VALIDATE_URL)) {
            $error = 'Введите корректную ссылку';
        } else {
            $link = $linkRepository->addLink($originalUrl);
        }
        return $this->render('link_data/create-form.html.twig', [
            'shortUrl' => $link->getFullShortUrl() ?? null,
            'error' => $error ?? null,
        ]);
    }

    public function getLink(string $slug, LinkRepository $linkRepository): Response
    {
        $link = $linkRepository->findOneBy(['shortUrl' => $slug]);

        if (!$link)
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
        $links = $linkRepository->findAll();
        return $this->render('link_data/list.html.twig', [
            'links' => $links,
        ]);
    }

    public function hideLinks(Request $request, LinkRepository $linkRepository): Response
    {
        $linksIds = $request->request->all('selected_links');

        foreach ($linksIds as &$linkId) {
            if (filter_var($linkId, FILTER_VALIDATE_INT))
                $linkId = (int)$linkId;
            else
                return new Response(
                    $this->renderView('link_data/error.html.twig', [
                        'error' => 'В веденных данных содержится ошибка',
                    ]),
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
        }

        $linkRepository->removeById($linksIds);

        return $this->redirectToRoute('getLinksList');
    }
}
