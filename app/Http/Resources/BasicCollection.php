<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class BasicCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\JsonResponse $response
     */
    public function withResponse($request, $response)
    {
        $jsonResponse = json_decode($response->getContent(), true);
        $link = $this->generateLinkHeader(collect($jsonResponse['links']));
        $response->header('Link', $link);

        $response->header('X-Total', $jsonResponse['meta']['total']);

        unset($jsonResponse['links'], $jsonResponse['meta']);
        $jsonResponse = $jsonResponse['data'];

        $response->setContent(json_encode($jsonResponse));
    }


    /**
     * @param Collection $links
     * @return string
     */
    protected function generateLinkHeader(Collection $links): string
    {
        $location = $links->filter()->map(function ($link, $rel) {
            if (empty($link) === false) {
                return 'rel="' . $rel . '", <' . $link . '>';
            }
        })->implode('; ');

        return $location;
    }


}