<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiTokenAuthenticator extends AbstractAuthenticator
{
    //VIDEO:
    // public function __construct(private UserRepository $repository)
    // {
    // }

    // public function supports(Request $request): ?bool
    // {
    //     // TODO: Implement supports() method.
    //     return $request->headers->has('X-AUTH-TOKEN');
    // }

    // public function authenticate(Request $request): Passport
    // {
    //     // TODO: Implement authenticate() method.
    //     $apiToken = $request->headers->get('X-AUTH-TOKEN');
    //     if (null === $apiToken) {
    //         // The token header was empty, authentication fails with HTTP Status
    //         // Code 401 "Unauthorized"
    //         throw new CustomUserMessageAuthenticationException('No API token provided');
    //     }

    //     // implement your own logic to get the user identifier from `$apiToken`
    //     // e.g. by looking up a user in the database using its API key
    //     $user = $this->repository->findOneBy(['apiToken' => $apiToken]);
    //     if (null === $user) {
    //         throw new UserNotFoundException();
    //     }

    //     return new SelfValidatingPassport(new UserBadge($user->getUserIdentifier()));
    // }

    // public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    // {
    //     // TODO: Implement onAuthenticationSuccess() method.
    //     return null;
    // }

    // public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    // {
    //     return new JsonResponse(
    //         // you may want to customize or obfuscate the message first
    //         // or to translate this message
    //         // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
    //         [
    //             'message' => strtr(
    //                 $exception->getMessageKey(),
    //                 $exception->getMessageData()
    //             )
    //         ],
    //         //status:
    //         Response::HTTP_UNAUTHORIZED
    //     );
    // }

    //COURS:
    public function __construct(private UserRepository $repository)
    {
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('X-AUTH-TOKEN');
    }

    public function authenticate(Request $request): Passport
    {
        $apiToken = $request->headers->get('X-AUTH-TOKEN');
        if (null === $apiToken) {
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }
        $user = $this->repository->findOneBy(['apiToken' => $apiToken]);
        if (null === $user) {
            throw new UserNotFoundException();
        }
        return new SelfValidatingPassport(new UserBadge($user->getUserIdentifier()));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {

        return new JsonResponse(
            ['message' => strtr($exception->getMessageKey(), $exception->getMessageData())],
            Response::HTTP_UNAUTHORIZED
        );
    }
}
