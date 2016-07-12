<?php


namespace Tagcade\Bundle\ApiBundle\Controller;


use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use Tagcade\Entity\Core\ImportedFile;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\Annotations as Rest;


/**
 * @Rest\RouteResource("ImportedFile")
 */
class ImportedFileController extends FOSRestController implements ClassResourceInterface {
    /**
     * Create a imported file from the submitted data
     *
     * @ApiDoc(
     *  section="ImportedFile",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param Request $request the request object
     *
     * @return mixed
     */
    public function postAction(Request $request)
    {
        $filePath   = $request->get('filePath');
        $publisher  = $request->get('publisher');
        $hash       = $request->get('hash');
        $hashType   = $request->get('hashType');

        $importedFile = new ImportedFile();
        $importedFile->setFilePath($filePath);
        $importedFile->setPublisherId($publisher);
        $importedFile->setHashType($hashType);
        $importedFile->setHash($hash);

        return $this->get('tagcade_app.domain_manager.imported_file')->save($importedFile);
    }

    /**
     *
     * @Rest\Get("importedFile/hash/{hashFile}/file")
     * Get a single imported file by hash value
     *
     * @ApiDoc(
     *  section="Imported File",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param  string $hashFile
     *
     * @return array
     */
    public function getByHashAction($hashFile)
    {
        return $this->get('tagcade_app.domain_manager.imported_file')->findByHash($hashFile);
    }
}