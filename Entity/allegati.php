<?php

namespace Fi\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * allegati
 */
class allegati {

  /**
   * @var integer
   */
  private $id;

  /**
   * @var string
   */
  private $nometabella;

  /**
   * @var integer
   */
  private $indicetabella;

  /**
   * @var string
   */
  private $allegato;

  /**
   * 
   * @Vich\UploadableField(mapping="file_allegati", fileNameProperty="allegato")
   * 
   * @var File
   */
  private $allegatofile;

  /**
   * @var \DateTime
   */
  private $datamodifica;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set nometabella
   *
   * @param string $nometabella
   *
   * @return allegati
   */
  public function setNometabella($nometabella) {
    $this->nometabella = $nometabella;

    return $this;
  }

  /**
   * Get nometabella
   *
   * @return string
   */
  public function getNometabella() {
    return $this->nometabella;
  }

  /**
   * Set indicetabella
   *
   * @param integer $indicetabella
   *
   * @return allegati
   */
  public function setIndicetabella($indicetabella) {
    $this->indicetabella = $indicetabella;

    return $this;
  }

  /**
   * Get indicetabella
   *
   * @return integer
   */
  public function getIndicetabella() {
    return $this->indicetabella;
  }

  /**
   * Set allegato
   *
   * @param string $allegato
   *
   * @return allegati
   */
  public function setAllegato($allegato) {
    $this->allegato = $allegato;

    return $this;
  }

  /**
   * Get allegato
   *
   * @return string
   */
  public function getAllegato() {
    return $this->allegato;
  }

  /**
   * Set allegatofile
   *
   * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $allegatofile
   *
   * @return allegati
   */
  public function setAllegatofile(File $allegatofile = null) {
    $this->allegatofile = $allegatofile;

    if ($image) {
      // It is required that at least one field changes if you are using doctrine
      // otherwise the event listeners won't be called and the file is lost
      $this->datamodifica = new \DateTime('now');
    }

    return $this;
  }

  /**
   * Get allegatofile
   *
   * @return string
   */
  public function getAllegatofile() {
    return $this->allegatofile;
  }

  /**
   * Set datamodifica
   *
   * @param \DateTime $datamodifica
   *
   * @return allegati
   */
  public function setDatamodifica($datamodifica) {
    $this->datamodifica = $datamodifica;

    return $this;
  }

  /**
   * Get datamodifica
   *
   * @return \DateTime
   */
  public function getDatamodifica() {
    return $this->datamodifica;
  }

}
