<?php

namespace AdminBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Exception\ValidatorException;

class FileUploader
{
    private $allowedMimetype = [
        'image/jpeg',
        'image/png',
        'image/gif',
    ];

    private $targetDirectory;
    /**
     * @var UploadedFile $uploadedFile
     */
    private $uploadedFile;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload()
    {
        $fileName = md5(uniqid() . mktime()) . '.' . $this->getUploadedFile()->guessExtension();
        $this->getUploadedFile()->move($this->getTargetDirectory(), $fileName);

        return $fileName;
    }

    public function validateImage()
    {
        $mimeType = $this->getUploadedFile()->getClientMimeType();
        if (!in_array($mimeType, $this->allowedMimetype)) {
            throw new ValidatorException("Invalid mime image type");
        }
    }
    
    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    /**
     * @return mixed
     */
    public function getUploadedFile()
    {
        return $this->uploadedFile;
    }

    /**
     * @param mixed $uploadedFile
     */
    public function setUploadedFile(UploadedFile $uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;
    }
}