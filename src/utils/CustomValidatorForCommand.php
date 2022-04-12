<?php
namespace App\utils;
use Symfony\Component\Console\Exception\InvalidArgumentException;


class CustomValidatorForCommand{
    public function validateEmail(?string $emailEntered):string{
        if(empty($emailEntered)){
            throw new InvalidArgumentException("veuillez saisir un email");
        }

        if(!filter_var($emailEntered, FILTER_VALIDATE_EMAIL))
        {
            throw new InvalidArgumentException("email saisie invalid");
        }

        return $emailEntered;
    }

    public function validatePassword(?string $plainPassword):string{
        if(empty($plainPassword)){
            throw new InvalidArgumentException("veuillez saisir un mot de passe ");
        }
        $passRegx = "/^(?=.*\d)(?=.*[A-Z])(?=.*[@#$%])(?!.*(.)\1{2}).*[a-z]/m";
        if(!preg_match($passRegx,  $plainPassword)){
            throw new InvalidArgumentException("le password doit contenir 8 caractères dont au moin majiscule au moin une miscule, au moin un caractere speciale et au moin un chiffre");
        }
        return $plainPassword;
    }
}