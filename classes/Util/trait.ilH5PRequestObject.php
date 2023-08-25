<?php

declare(strict_types=1);

use srag\Plugins\H5P\RequestHelper;
use srag\Plugins\H5P\ArrayBasedRequestWrapper;
use srag\Plugins\H5P\IRequestParameters;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
trait ilH5PRequestObject
{
    use RequestHelper;

    /**
     * Returns the currently requested repository object, which is found by either its
     * reference id (ref_id) or target in case of ILIAS goto-links.
     */
    protected function getRequestedRepositoryObject(ArrayBasedRequestWrapper $request): ?ilObject
    {
        $ref_id = $this->getRequestedReferenceId($request);
        if (null === $ref_id) {
            return null;
        }

        $object = ilObjectFactory::getInstanceByRefId($ref_id, false);

        return ($object) ?: null;
    }

    /**
     * Returns the currently requested ILIAS object, regardless of the current context.
     * The object will be found directly by its object-id (obj_id).
     */
    protected function getRequestedObject(ArrayBasedRequestWrapper $request): ?ilObject
    {
        $obj_id = $this->getRequestedInteger($request, IRequestParameters::OBJ_ID);
        if (null === $obj_id) {
            return null;
        }

        $object = ilObjectFactory::getInstanceByObjId($obj_id, false);

        return ($object) ?: null;
    }
}
