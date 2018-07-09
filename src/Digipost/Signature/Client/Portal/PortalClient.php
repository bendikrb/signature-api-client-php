<?php

namespace Digipost\Signature\Client\Portal;

use Digipost\Signature\Client\ASiCe\CreateASiCE;
use Digipost\Signature\Client\ASiCe\Manifest\CreatePortalManifest;
use Digipost\Signature\Client\ClientConfiguration;
use Digipost\Signature\Client\Core\DeleteDocumentsUrl;
use Digipost\Signature\Client\Core\Internal\Cancellable;
use Digipost\Signature\Client\Core\Internal\ClientHelper;
use Digipost\Signature\Client\Core\Internal\Http\SignatureHttpClientFactory;
use Digipost\Signature\Client\Core\PAdESReference;
use Digipost\Signature\Client\Core\Sender;
use Digipost\Signature\Client\Core\XAdESReference;
use Digipost\Signature\Client\Direct\DirectJobResponse;
use Digipost\Signature\Client\Direct\JaxbEntityMapping;
//use Digipost\Signature\Client\Portal\PortalJobStatusChanged;

class PortalClient {

    private $client;
    private $aSiCECreator;
    private $clientConfiguration;

    public function __construct(ClientConfiguration $config) {
        $this->clientConfiguration = $config;
        $this->client = new ClientHelper(SignatureHttpClientFactory::create($config), $config->getGlobalSender());
        $this->aSiCECreator = new CreateASiCE(new CreatePortalManifest($config->getClock()), $config);
    }


    public function create(PortalJob $job) {
        $documentBundle = $this->aSiCECreator->createASiCE($job);
        $signatureJobRequest = $this->toJaxb($job, $this->clientConfiguration->getGlobalSender());

        $xmlPortalSignatureJobResponse = $this->client->sendPortalSignatureJobRequest($signatureJobRequest, $documentBundle, $job->getSender());
        return $this->fromJaxb($xmlPortalSignatureJobResponse);
    }


  /**
   * If there is a job with an updated {@link PortalJobStatus status}, the returned object contains
   * necessary information to act on the status change. The returned object can be queried using
   * {@link PortalJobStatusChanged#is(PortalJobStatus) .is(}{@link PortalJobStatus#NO_CHANGES NO_CHANGES)}
   * to determine if there has been a status change. When processing of the status change is complete, (e.g. retrieving
   * {@link #getPAdES(PAdESReference) PAdES} and/or {@link #getXAdES(XAdESReference) XAdES} documents for a
   * {@link PortalJobStatus#COMPLETED_SUCCESSFULLY completed} job where all signers have {@link SignatureStatus signed} their documents),
   * the returned status must be {@link #confirm(PortalJobStatusChanged) confirmed}.
   *
   * @param Sender|null $sender
   *
   * @return DirectJobResponse,
   *         never {@code null}.
   */

    public function getStatusChange(Sender $sender = NULL) {
        $statusChangeResponse = $this->client->getPortalStatusChange($sender);
        if ($statusChangeResponse->gotStatusChange()) {
            return JaxbEntityMapping::fromJaxb($statusChangeResponse);
        } else {
            return $this->noUpdatedStatus($statusChangeResponse->getNextPermittedPollTime());
        }
    }


  /**
   * Confirms that the status retrieved from {@link #getStatusChange()} is received and may
   * be discarded by the Signature service and not retrieved again. Calling this method on
   * a status update with no {@link ConfirmationReference} has no effect.
   *
   * @param \Digipost\Signature\Client\Portal\PortalJobStatusChanged $receivedStatusChanged
   */
    public function confirm(PortalJobStatusChanged $receivedStatusChanged) {
        $this->client->confirm($receivedStatusChanged);
    }

    public function cancel(Cancellable $cancellable) {
        $this->client->cancel($cancellable);
    }

    public function getXAdES(XAdESReference $xAdESReference) {
        return $this->client->getSignedDocumentStream($xAdESReference->getxAdESUrl());
    }


    public function getPAdES(PAdESReference $pAdESReference) {
        return $this->client->getSignedDocumentStream($pAdESReference->getpAdESUrl());
    }

    public function deleteDocuments(DeleteDocumentsUrl $deleteDocumentsUrl) {
        $this->client->deleteDocuments($deleteDocumentsUrl);
    }

}
