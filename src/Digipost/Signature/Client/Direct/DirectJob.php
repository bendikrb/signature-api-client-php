<?php

namespace Digipost\Signature\Client\Direct;

use Digipost\Signature\Client\Core\AuthenticationLevel;
use Digipost\Signature\Client\Core\IdentifierInSignedDocuments;
use Digipost\Signature\Client\Core\Internal\JobCustomizations;
use Digipost\Signature\Client\Core\Sender;
use Digipost\Signature\Client\Core\JOB;

/**
 * Class DirectJob
 *
 * @package Digipost\Signature\Client\Direct
 */
class DirectJob implements JOB, WithExitUrls {

  private $reference;

  /** @var DirectSigner[] $signers */
  private $signers;

  /**
   * @var DirectDocument
   */
  private $document;

  /**
   * @var String
   */
  private $completionUrl;

  /**
   * @var String
   */
  private $rejectionUrl;

  /**
   * @var String
   */
  private $errorUrl;

  /**
   * @var Sender
   */
  private $sender;

  /**
   * @var StatusRetrievalMethod
   */
  private $statusRetrievalMethod;

  /** @var AuthenticationLevel $requiredAuthentication */
  private $requiredAuthentication;

  /**
   * @var IdentifierInSignedDocuments
   */
  private $identifierInSignedDocuments;

  function __construct(array $signers, DirectDocument $document,
                       String $completionUrl, String $rejectionUrl,
                       String $errorUrl) {
    $this->signers = $signers;
    $this->document = $document;
    $this->completionUrl = $completionUrl;
    $this->rejectionUrl = $rejectionUrl;
    $this->errorUrl = $errorUrl;
  }

  public function getReference() {
    return $this->reference;
  }

  public function getSender() {
    return $this->sender;
  }

  public function getDocument() {
    return $this->document;
  }

  public function getCompletionUrl() {
    return $this->completionUrl;
  }

  public function getRejectionUrl() {
    return $this->rejectionUrl;
  }

  public function getErrorUrl() {
    return $this->errorUrl;
  }

  public function getRequiredAuthentication() {
    return $this->requiredAuthentication;
  }

  public function getIdentifierInSignedDocuments() {
    return $this->identifierInSignedDocuments;
  }

  public function getSigners() {
    return $this->signers;
  }

  public function getStatusRetrievalMethod() {
    return $this->statusRetrievalMethod;
  }


  /**
   * Create a new DirectJob.
   *
   * @param DirectDocument $document    The {@link DirectDocument} that should be signed.
   * @param WithExitUrls   $hasExitUrls specifies the urls the user will be redirected back to upon completing/rejecting/failing
   *                                    the signing ceremony. See {@link \Digipost\Signature\Client\Direct\ExitUrls::of ExitUrls::of(String, String, String)}, and alternatively
   *                                    {@link \Digipost\Signature\Client\Direct\ExitUrls::singleExitUrl ExitUrls::singleExitUrl(String)}.
   * @param DirectSigner[] $signers     The {@link \Digipost\Signature\Client\Direct\DirectSigner DirectSigners} of the document.
   *
   * @return DirectJobBuilder
   */
  public static function builder(DirectDocument $document,
                                 WithExitUrls $hasExitUrls,
                                 DirectSigner... $signers) {
    $signers = is_array($signers) ? $signers : [$signers];
    return new DirectJobBuilder(
      $signers,
      $document,
      $hasExitUrls->getCompletionUrl(),
      $hasExitUrls->getRejectionUrl(), $hasExitUrls->getErrorUrl());
  }

  /**
   * @param mixed $reference
   */
  public function setReference($reference) {
    $this->reference = $reference;
  }

  /**
   * @param Sender $sender
   */
  public function setSender(Sender $sender) {
    $this->sender = $sender;
  }

  /**
   * @param IdentifierInSignedDocuments $identifierInSignedDocuments
   */
  public function setIdentifierInSignedDocuments(IdentifierInSignedDocuments $identifierInSignedDocuments) {
    $this->identifierInSignedDocuments = $identifierInSignedDocuments;
  }

  /**
   * @param AuthenticationLevel $requiredAuthentication
   */
  public function setRequiredAuthentication(AuthenticationLevel $requiredAuthentication) {
    $this->requiredAuthentication = $requiredAuthentication;
  }

  /**
   * @param mixed $statusRetrievalMethod
   */
  public function setStatusRetrievalMethod(StatusRetrievalMethod $statusRetrievalMethod) {
    $this->statusRetrievalMethod = $statusRetrievalMethod;
  }

  public function __toString() {
    return self::class . ' with reference ' . $this->getReference();
  }
}

class DirectJobBuilder implements JobCustomizations {

  /** @var DirectJob */
  private $target;

  private $built = FALSE;

  function __construct(array $signers, DirectDocument $document,
                       String $completionUrl, String $rejectionUrl,
                       String $errorUrl) {
    $this->target = new DirectJob($signers, $document, $completionUrl,
                                  $rejectionUrl, $errorUrl);
  }

  /** @inheritdoc */
  public function withSender(Sender $sender) {
    $this->target->setSender($sender);
    return $this;
  }

  /** @inheritdoc */
  public function requireAuthentication(AuthenticationLevel $level) {
    $this->target->setRequiredAuthentication($level);
    return $this;
  }

  /** @inheritdoc */
  public function withIdentifierInSignedDocuments(IdentifierInSignedDocuments $identifier) {
    $this->target->setIdentifierInSignedDocuments($identifier);
    return $this;
  }

  public function retrieveStatusBy(StatusRetrievalMethod $statusRetrievalMethod) {
    $this->target->setStatusRetrievalMethod($statusRetrievalMethod);
    return $this;
  }

  public function build() {
    if ($this->built) {
      throw new \RuntimeException("Can't build twice");
    }
    $this->built = TRUE;
    return $this->target;
  }

  /** @inheritdoc */
  function withReference(String $reference) {
    $this->target->setReference($reference);
    return $this;
  }
}
