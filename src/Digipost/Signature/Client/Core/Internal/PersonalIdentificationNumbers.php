<?php
namespace Digipost\Signature\Client\Core\Internal;

class PersonalIdentificationNumbers {

  public static function mask($personalIdentificationNumber) // [String personalIdentificationNumber]
  {
    if (!isset($personalIdentificationNumber)) {
      return NULL;
    }
    else {
      if (strlen($personalIdentificationNumber) < 6) {
        return $personalIdentificationNumber;
      }
    }
    $masking = str_repeat('*', strlen($personalIdentificationNumber) - 6);

    //return ($personalIdentificationNumber->substring(0, 6) . new String($masking));
    return substr($personalIdentificationNumber, 0, 6) . $masking;
  }
}


