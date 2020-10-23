<?php

namespace App\Tests\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\BlogPost;
use App\Entity\User;
use App\EventSubscriber\AuthoredEntitySubscriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AuthoredEntitySubscriberTest extends TestCase
{
    public function testConfiguration()
    {
        $result = AuthoredEntitySubscriber::getSubscribedEvents();
        $this->assertArrayHasKey(KernelEvents::VIEW, $result);
        $this->assertEquals(
            ['getAuthenticatedUser', EventPriorities::PRE_WRITE],
            $result[KernelEvents::VIEW]
        );
    }

    public function testSetAuthorCall()
    {
        $entityMock = $this->getEntityMock(BlogPost::class, true);
        $tokenStorageMock = $this->getTokenStorageMock();
        $eventMock = $this->getEventMock("POST", $entityMock);

        (new AuthoredEntitySubscriber($tokenStorageMock))->getAuthenticatedUser(
            $eventMock
        );

        // Non existing class
        $nonExistingEntityMock = $this->getEntityMock("NonExisting", false);
        $tokenStorageMock = $this->getTokenStorageMock();
        $eventMock = $this->getEventMock("GET", $nonExistingEntityMock);

        (new AuthoredEntitySubscriber($tokenStorageMock))->getAuthenticatedUser(
            $eventMock
        );
    }

    /**
     * @return MockObject|TokenStorageInterface
     */
    private function getTokenStorageMock()
    {
        // Mocked "TokenInterface::class"
        $tokenMock = $this->getMockBuilder(TokenInterface::class)
            ->getMockForAbstractClass();

        // Call "TokenInterface::getUser()"
        $tokenMock->expects($this->once())
            ->method("getUser")
            ->willReturn(new User());

        // Mocked "TokenStorageInterface::class"
        $tokenStorageMock = $this->getMockBuilder(TokenStorageInterface::class)
            ->getMockForAbstractClass();

        // Call "TokenStorageInterface::getToken()"
        $tokenStorageMock->expects($this->once())
            ->method("getToken")
            ->willReturn($tokenMock);

        return $tokenStorageMock;
    }

    /**
     * @return MockObject|ViewEvent
     */
    private function getEventMock(string $method, $controllerResult)
    {
        // Mocked "ViewEvent::class"
        $eventMock = $this->getMockBuilder(ViewEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Call "ViewEvent::getControllerResult()"
        $eventMock->expects($this->once())
            ->method("getControllerResult")
            ->willReturn($controllerResult);

        // Mocked "Request::class"
        $requestMock = $this->getMockBuilder(Request::class)
            ->getMock();

        // Call "Request::getMethod()"
        $requestMock->expects($this->once())
            ->method("getMethod")
            ->willReturn($method);

        // Call "Request::getRequest()"
        $eventMock->expects($this->once())
            ->method("getRequest")
            ->willReturn($requestMock);

        return $eventMock;
    }

    /**
     * @param string $className
     * @return BlogPost|MockObject
     */
    private function getEntityMock(string $className, bool $shouldCallSetAuthor)
    {
        // Mocked entity and method "BlogPost::setAuthor"
        $entityMock = $this->getMockBuilder($className)
            ->setMethods(["setAuthor"])
            ->getMock();

        // Call "BlogPost::setAuthor()"
        $entityMock->expects($shouldCallSetAuthor ? $this->once() : $this->never())
            ->method("setAuthor");
        return $entityMock;
    }
}