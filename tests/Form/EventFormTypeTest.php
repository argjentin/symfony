<?php

namespace App\Tests\Form;

use App\Form\EventFormType;
use App\Entity\Event;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\TestCase;


class EventFormTypeTest extends TestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'title' => 'Event Title',
            'description' => 'Event Description',
            'datetime' => '2021-01-01 00:00:00',
            'maxParticipants' => 100,
            'public' => true,
        ];

        $model = new Event();
        $form = $this->factory->create(EventFormType::class, $model);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($model->getTitle(), $formData['title']);
        $this->assertEquals($model->getDescription(), $formData['description']);
    }
}