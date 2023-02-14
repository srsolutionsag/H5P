<?php

declare(strict_types=1);

namespace srag\Plugins\H5P;

use ILIAS\Refinery\Transformation;
use ILIAS\Refinery\Factory as Refinery;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
trait RequestHelper
{
    /**
     * @var ArrayBasedRequestWrapper
     */
    protected $post_request;

    /**
     * @var ArrayBasedRequestWrapper
     */
    protected $get_request;

    /**
     * @var Refinery
     */
    protected $refinery;

    protected function getRequestedInteger(ArrayBasedRequestWrapper $request, string $parameter_name): ?int
    {
        return $this->getRequestedParameter($request, $parameter_name, $this->refinery->kindlyTo()->int());
    }

    protected function getRequestedString(ArrayBasedRequestWrapper $request, string $parameter_name): ?string
    {
        return $this->getRequestedParameter($request, $parameter_name, $this->refinery->kindlyTo()->string());
    }

    /**
     * @return mixed|null
     */
    protected function getRequestedMixed(ArrayBasedRequestWrapper $request, string $parameter_name)
    {
        return $this->getRequestedParameter($request, $parameter_name, $this->getMixedTransformation());
    }

    /**
     * @return mixed|null
     */
    private function getRequestedParameter(
        ArrayBasedRequestWrapper $request,
        string $parameter_name,
        Transformation $transformation
    ) {
        if ($request->has($parameter_name)) {
            return $request->retrieve($parameter_name, $transformation);
        }

        return null;
    }

    private function getMixedTransformation(): Transformation
    {
        return $this->refinery->custom()->transformation(
            static function ($value) {
                return $value;
            }
        );
    }
}
