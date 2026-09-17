<?php

// ================= CUSTOM EXCEPTION CLASS =================

class AgeException extends Exception
{
    public function errorMessage()
    {
        return "Invalid Age: " . $this->getMessage();
    }
}


// ================= TRY-CATCH BLOCK =================

try {

    $age = 15;

    // Check age
    if ($age < 18) {
        throw new AgeException("Age must be 18 or above.");
    }

    echo "You are eligible.";

}
catch (AgeException $e) {

    echo "Custom Exception: " . $e->errorMessage();

}
catch (Exception $e) {

    echo "Exception: " . $e->getMessage();

}

?>