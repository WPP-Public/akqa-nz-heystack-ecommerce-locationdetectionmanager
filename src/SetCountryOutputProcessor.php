<?php

namespace Heystack\LocationDetection;

use Heystack\Core\Identifier\Identifier;
use Heystack\Core\Output\ProcessorInterface;

class SetCountryOutputProcessor implements ProcessorInterface
{
    /**
     * Returns the identifier of the processor
     * @return \Heystack\Core\Identifier\Identifier
     */
    public function getIdentifier()
    {
        return new Identifier('country');
    }

    /**
     * Executes the main functionality of the output processor
     *
     * @param \Controller $controller The relevant SilverStripe controller
     * @param mixed $result The result from the input processor
     * @return \SS_HTTPResponse
     */
    public function process(\Controller $controller, $result = null)
    {
        $response = $controller->getResponse();

        $response->setStatusCode(200);
        $response->addHeader('Content-Type', 'application/json');
        $response->setBody(json_encode([
            'success' => (bool) $result
        ]));
        
        return $response;
    }
}