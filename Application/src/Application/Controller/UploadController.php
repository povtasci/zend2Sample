<?php
namespace Application\Controller;

use Zend\View\Model\JsonModel;
use Application\Custom\HashGenerator;
use Application\Custom\SimpleImage;

class UploadController extends RootController
{
    const IMG_UPLOAD_DIR = "/media/image/";
    const PDF_UPLOAD_DIR = "/media/pdf/";

    public function imageAction()
    {
        try {
            $path_start = PUBLIC_PATH . self::IMG_UPLOAD_DIR;
            $url_start = self::IMG_UPLOAD_DIR;

            $request = $this->getRequest();
            if ($request->isPost()) {
                $adapter = new \Zend\File\Transfer\Adapter\Http();
                $adapter->setDestination($path_start);
                $filesize  = new \Zend\Validator\File\Size(array('max' => 10000000 )); // 10MB
                $extension = new \Zend\Validator\File\Extension(array('extension' => array('jpg', 'jpeg', 'png')));
                foreach ($adapter->getFileInfo() as $file => $info) {
                    $name = $adapter->getFileName($file);
                    $ext = "." . pathinfo($name, PATHINFO_EXTENSION);
                    $adapter->setValidators(array($filesize, $extension), $info['name']);

                    $name_without_ext = time() . '_' . strtolower(HashGenerator::generate(6));

                    $imageDefault = array(
                        "PATH" => $path_start . $name_without_ext . $ext,
                        "URL" => $url_start . $name_without_ext . $ext,
                    );
                    $width = 100;
                    $height = 150;
                    $imageMedium = array(
                        "PATH" => $path_start . $name_without_ext . "_" . $width . "x" . $height . $ext,
                        "URL" => $url_start . $name_without_ext . "_" . $width . "x" . $height . $ext,
                    );

                    $adapter->addFilter(
                        new \Zend\Filter\File\Rename(array('target' => $imageDefault['PATH'],
                            'overwrite' => true)),
                        null, $file
                    );

                    if($adapter->isValid()) {
                        if($adapter->receive($info['name'])) {
                            $simpleImage = new SimpleImage();

                            $simpleImage->load($imageDefault['PATH']);
                            $simpleImage->thumbnail($width, $height);
                            $simpleImage->save($imageMedium['PATH']);
                        }
                    } else {
                        return new JsonModel(array(
                            'returnCode' => 201,
                            'msg' => "Allowed only .png, .jpg. .jpeg files (max 10MB).",
                        ));
                    }
                    if ($adapter->receive($file)) {
                    }
                }
            }

            return new JsonModel(array(
                'returnCode' => 101,
                'result' => array(
                    'url' => $imageDefault['URL'],
                ),
                'msg' => 'Image has been uploaded',
            ));
        } catch (\Exception $e) {
            return new JsonModel(array(
                'returnCode' => 201,
                'msg' => $e->getMessage(),
            ));
        }
    }

    public function pdfAction()
    {
        try {
            $path_start = PUBLIC_PATH . self::PDF_UPLOAD_DIR;
            $url_start = self::PDF_UPLOAD_DIR;

            $request = $this->getRequest();
            if ($request->isPost()) {
                $adapter = new \Zend\File\Transfer\Adapter\Http();
                $adapter->setDestination($path_start);
                $filesize  = new \Zend\Validator\File\Size(array('max' => 10000000 )); // 10MB
                $extension = new \Zend\Validator\File\Extension(array('extension' => array('pdf')));
                foreach ($adapter->getFileInfo() as $file => $info) {
                    $name = $adapter->getFileName($file);
                    $ext = "." . pathinfo($name, PATHINFO_EXTENSION);
                    $adapter->setValidators(array($filesize, $extension), $info['name']);

                    $name_without_ext = time() . '_' . strtolower(HashGenerator::generate(6));

                    $pdfDefault = array(
                        "PATH" => $path_start . $name_without_ext . $ext,
                        "URL" => $url_start . $name_without_ext . $ext,
                    );

                    $adapter->addFilter(
                        new \Zend\Filter\File\Rename(array(
                            'target' => $pdfDefault['PATH'],
                            'overwrite' => true
                        )),
                        null, $file
                    );

                    if($adapter->isValid()) {
                        $adapter->receive($info['name']);
                    } else {
                        return new JsonModel(array(
                            'returnCode' => 201,
                            'msg' => "Allowed only pdf files (max 10MB).",
                        ));
                    }
                }
            }

            return new JsonModel(array(
                'returnCode' => 101,
                'result' => array(
                    'url' => $pdfDefault['URL'],
                ),
                'msg' => 'Image has been uploaded.',
            ));
        } catch (\Exception $e) {
            return new JsonModel(array(
                'returnCode' => 201,
                'msg' => $e->getMessage(),
            ));
        }
    }
}