<?php
namespace Iredeem\Vendor\Test\Unit\Model\Batch\Export\File;

/**
 * Class EmailTest
 * @package Iredeem\Vendor\Test\Unit\Model\Batch\Export\File
 */
class EmailTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    protected $_testClassName = \Iredeem\Vendor\Model\Batch\Export\File\Email::class;

    /**
     * @var \Iredeem\Vendor\Model\Batch\Export\File\Email
     */
    protected $_testClass;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_vendorHelper;

    /**
     * Method ran before each test
     */
    public function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->_vendorHelper = $this->createPartialMock(
            \Iredeem\Vendor\Helper\Data::class,
            ['getBatchOrderExportConfig']
        );
        $this->_testClass = $objectManager->getObject(
            'Iredeem\Vendor\Model\Batch\Export\File\Email',
            [
                'vendorHelper' => $this->_vendorHelper
            ]
        );
    }

    /**
     * Test for setSender method
     */
    public function testSetSender()
    {
        $email = 'email@example.com';
        $name = 'name email';

        $expectedResult = [
            'email' => 'email@example.com',
            'name' => 'name email'
        ];
        $result = $this->_testClass->setSender($email, $name)->getSender();

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for setTemplateVars method
     */
    public function testSetTemplateVars()
    {
        $this->_testClass->setBody('body');
        $this->_testClass->setSubject('subject');

        $expectedResult = [
            'body' => 'body',
            'subject' => 'subject'
        ];
        $result = $this->_testClass->setTemplateVars()->getTemplateVars();

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for addTemplateVar method
     */
    public function testAddTemplateVar()
    {
        $expectedResult = [
            'name' => 'value'
        ];
        $result = $this->_testClass->addTemplateVar('name', 'value')->getTemplateVars();

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for addNote method
     */
    public function testAddNote()
    {
        $note = 'note';
        $anotherNote = 'another note';

        $expectedResult = 'note' . PHP_EOL . 'another note';
        $result = $this->_testClass->addNote($note)->addNote($anotherNote)->getNotes();

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for _checkEmail method
     */
    public function testCheckEmailIfString()
    {
        $checkMail = new \ReflectionMethod($this->_testClassName, '_checkEmail');
        $checkMail->setAccessible(true);
        $email = 'email@example.com';
        $settingName = 'recipient';

        $expectedResult = 'email@example.com';
        $result = $checkMail->invokeArgs($this->_testClass, [$email, $settingName]);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for _checkEmail method
     */
    public function testCheckEmailIfStringInvalid()
    {
        $checkMail = new \ReflectionMethod($this->_testClassName, '_checkEmail');
        $checkMail->setAccessible(true);
        $email = 'emailexample.com';
        $settingName = 'recipient';

        $result = $checkMail->invokeArgs($this->_testClass, [$email, $settingName]);
        $notesMessage = '"recipient" setting "emailexample.com" is not set properly.';
        $notes = $this->_testClass->getNotes();

        $this->assertFalse($result);
        $this->assertContains($notesMessage, $notes);
    }

    /**
     * Test for _checkEmail method
     */
    public function testCheckEmailIfArray()
    {
        $checkMail = new \ReflectionMethod($this->_testClassName, '_checkEmail');
        $checkMail->setAccessible(true);
        $email = ['bcc' => 'email@example.com'];
        $settingName = 'bcc';

        $expectedResult = 'email@example.com';
        $result = $checkMail->invokeArgs($this->_testClass, [$email, $settingName]);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for _checkEmail method
     */
    public function testCheckEmailMultipleEmailsInArray()
    {
        $checkMail = new \ReflectionMethod($this->_testClassName, '_checkEmail');
        $checkMail->setAccessible(true);
        $email = ['bcc' => ['email@example.com ', 'email1@example.com']];
        $settingName = 'bcc';

        $expectedResult = ['email@example.com', 'email1@example.com'];
        $result = $checkMail->invokeArgs($this->_testClass, [$email, $settingName]);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for _checkEmail method
     */
    public function testCheckEmailMultipleEmailsInArrayInvalidEmail()
    {
        $checkMail = new \ReflectionMethod($this->_testClassName, '_checkEmail');
        $checkMail->setAccessible(true);
        $email = ['bcc' => ['email@example.com ', 'email1@example.com', 'wrongEmail.com']];
        $settingName = 'bcc';

        $expectedResult = ['email@example.com', 'email1@example.com'];
        $expectedNotesMessage = '"bcc" setting "wrongEmail.com" is not set properly.';
        $result = $checkMail->invokeArgs($this->_testClass, [$email, $settingName]);

        $this->assertEquals($expectedResult, $result);
        $this->assertEquals($expectedNotesMessage, $this->_testClass->getNotes());
    }

    /**
     * Test for _checkEmail method
     */
    public function testCheckEmailIfArrayInvalid()
    {
        $checkMail = new \ReflectionMethod($this->_testClassName, '_checkEmail');
        $checkMail->setAccessible(true);
        $email = ['bcc' => 'emailexample.com'];
        $settingName = 'bcc';

        $result = $checkMail->invokeArgs($this->_testClass, [$email, $settingName]);
        $notesMessage = '"bcc" setting "emailexample.com" is not set properly.';
        $notes = $this->_testClass->getNotes();

        $this->assertFalse($result);
        $this->assertContains($notesMessage, $notes);
    }

    /**
     * Test for _checkEmail method
     */
    public function testCheckEmailIfArrayNotExistent()
    {
        $checkMail = new \ReflectionMethod($this->_testClassName, '_checkEmail');
        $checkMail->setAccessible(true);
        $email = ['bcc' => 'emailexample.com'];
        $settingName = 'cc';

        $result = $checkMail->invokeArgs($this->_testClass, [$email, $settingName]);

        $this->assertFalse($result);
    }

    /**
     * Test for _getEmailComponent method
     */
    public function testGetEmailComponent()
    {
        $emailComponent = new \ReflectionMethod($this->_testClassName, '_getEmailComponent');
        $emailComponent->setAccessible(true);
        $componentArray = [
            'cc' => 'email@example.com',
            'subject' => 'Subject'
        ];
        $settingName = 'subject';
        $isEmailFormat = false;

        $expectedResult = 'Subject';
        $result = $emailComponent->invokeArgs($this->_testClass, [$componentArray, $settingName, $isEmailFormat]);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for _getEmailComponent method
     */
    public function testGetEmailComponentEmailFormat()
    {
        $emailComponent = new \ReflectionMethod($this->_testClassName, '_getEmailComponent');
        $emailComponent->setAccessible(true);
        $componentArray = [
            'cc' => 'email@example.com',
            'subject' => 'Subject'
        ];
        $settingName = 'cc';
        $isEmailFormat = true;

        $expectedResult = 'email@example.com';
        $result = $emailComponent->invokeArgs($this->_testClass, [$componentArray, $settingName, $isEmailFormat]);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for _getEmailComponent method
     */
    public function testGetEmailComponentWithDefaultValue()
    {
        $emailComponent = new \ReflectionMethod($this->_testClassName, '_getEmailComponent');
        $emailComponent->setAccessible(true);

        $this->_vendorHelper->expects($this->once())
            ->method('getBatchOrderExportConfig')
            ->will($this->returnValue('Subject'));
        $componentArray = [
            'cc' => 'email@example.com',
        ];
        $settingName = 'subject';
        $isEmailFormat = false;

        $expectedResult = 'Subject';
        $result = $emailComponent->invokeArgs($this->_testClass, [$componentArray, $settingName, $isEmailFormat]);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for _getEmailComponent method
     */
    public function testGetEmailComponentWithNoDefaultValue()
    {
        $emailComponent = new \ReflectionMethod($this->_testClassName, '_getEmailComponent');
        $emailComponent->setAccessible(true);

        $componentArray = [
            'cc' => 'email@example.com',
        ];
        $settingName = 'bcc';
        $isEmailFormat = true;

        $result = $emailComponent->invokeArgs($this->_testClass, [$componentArray, $settingName, $isEmailFormat]);

        $this->assertFalse($result);
    }

    /**
     * Test for _checkMultipleEmails method
     */
    public function testCheckMultipleEmails()
    {
        $multipleEmails = new \ReflectionMethod($this->_testClassName, '_checkMultipleEmails');
        $multipleEmails->setAccessible(true);

        $emailsArray = ['test@example.com ', 'test1@example.com'];
        $settingName = 'bcc';

        $expectedResult = ['test@example.com', 'test1@example.com'];
        $result = $multipleEmails->invokeArgs($this->_testClass, [$emailsArray, $settingName]);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for _checkMultipleEmails method
     */
    public function testCheckMultipleEmailsInvalidEmail()
    {
        $multipleEmails = new \ReflectionMethod($this->_testClassName, '_checkMultipleEmails');
        $multipleEmails->setAccessible(true);

        $emailsArray = ['test@example.com', 'test1@example.com' . PHP_EOL, 'wrongEmailFormat.com'];
        $settingName = 'bcc';

        $expectedResult = ['test@example.com', 'test1@example.com'];
        $notesExpectedResult = '"bcc" setting "wrongEmailFormat.com" is not set properly.';
        $result = $multipleEmails->invokeArgs($this->_testClass, [$emailsArray, $settingName]);

        $this->assertEquals($expectedResult, $result);
        $this->assertEquals($notesExpectedResult, $this->_testClass->getNotes());
    }

    /**
     * Test for _checkSingleEmail method
     */
    public function testCheckSingleEmail()
    {
        $singleEmail = new \ReflectionMethod($this->_testClassName, '_checkSingleEmail');
        $singleEmail->setAccessible(true);

        $email = 'test@example.com ';
        $settingName = 'bcc';

        $expectedResult = 'test@example.com';
        $result = $singleEmail->invokeArgs($this->_testClass, [$email, $settingName]);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for _checkSingleEmail method
     */
    public function testCheckSingleEmailInvalidString()
    {
        $singleEmail = new \ReflectionMethod($this->_testClassName, '_checkSingleEmail');
        $singleEmail->setAccessible(true);

        $email = 'testexample.com ';
        $settingName = 'bcc';

        $result = $singleEmail->invokeArgs($this->_testClass, [$email, $settingName]);
        $notesExpectedResult = '"bcc" setting "testexample.com " is not set properly.';

        $this->assertFalse($result);
        $this->assertEquals($notesExpectedResult, $this->_testClass->getNotes());
    }
}
