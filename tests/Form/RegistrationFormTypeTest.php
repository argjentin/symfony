<?php

namespace App\Tests\Form;

use App\Form\RegistrationFormType;
use App\Entity\User;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;

class RegistrationFormTypeTest extends TypeTestCase
{
    protected function getExtensions()
    {
        $validator = Validation::createValidator();
        return [
            new ValidatorExtension($validator)
        ];
    }

    public function testSubmitValidData()
    {
        $formData = [
            'email' => 'test10@example.com',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'plainPassword' => ['first' => 'password123', 'second' => 'password123']
        ];

        $model = new User();
        $form = $this->factory->create(RegistrationFormType::class, $model);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($model->getEmail(), $formData['email']);
        $this->assertEquals($model->getFirstName(), $formData['firstName']);
    }
}
