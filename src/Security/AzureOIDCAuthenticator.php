<?php

namespace App\Security;

use App\Entity\Tenant;
use App\Entity\User;
use App\Enum\UserTypeEnum;
use App\Repository\TenantRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use ItkDev\OpenIdConnect\Exception\ItkOpenIdConnectException;
use ItkDev\OpenIdConnectBundle\Exception\InvalidProviderException;
use ItkDev\OpenIdConnectBundle\Security\OpenIdConfigurationProviderManager;
use ItkDev\OpenIdConnectBundle\Security\OpenIdLoginAuthenticator;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class AzureOIDCAuthenticator extends OpenIdLoginAuthenticator
{
    private const string BLAMABLE_IDENTIFIER = 'OIDC';

    /**
     * AzureOIDCAuthenticator constructor.
     */
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TenantRepository $tenantRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly UrlGeneratorInterface $router,
        OpenIdConfigurationProviderManager $providerManager,
    ) {
        parent::__construct($providerManager);
    }

    public function authenticate(Request $request): Passport
    {
        try {
            // Validate claims
            $claims = $this->validateClaims($request);

            $tenant = $this->getTenant($claims);
            $user = $this->getUser($claims, $tenant);

            $this->entityManager->flush();

            return new SelfValidatingPassport(new UserBadge($user->getUserIdentifier()));
        } catch (ItkOpenIdConnectException|InvalidProviderException|ClientExceptionInterface $exception) {
            throw new CustomUserMessageAuthenticationException($exception->getMessage());
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->router->generate('admin'));
    }

    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        return new RedirectResponse($this->router->generate('itkdev_openid_connect_login', [
            'providerKey' => 'admin',
        ]));
    }

    private function getTenant(array $claims): Tenant
    {
        $tenantName = $claims['magistratsafdeling'];

        $tenant = $this->tenantRepository->findOneBy(['name' => $tenantName]);
        if (null === $tenant) {
            $tenant = new Tenant($tenantName);
            $this->entityManager->persist($tenant);
            $tenant->setCreatedBy(self::BLAMABLE_IDENTIFIER);
        }

        return $tenant;
    }

    private function getUser(array $claims, Tenant $tenant): User
    {
        // Extract properties from claims
        $name = $claims['kaldenavn'];
        $email = $claims['upn'];
        $roles = $this->getNormalizedRoles($claims['role']);

        // Check if the user exists already - if not, create a user
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (null === $user) {
            // Create the new user and persist it
            $user = new User($email, $tenant);
            $this->entityManager->persist($user);
            $user->setProviderId($email);
            $user->setUserType(UserTypeEnum::OIDC_INTERNAL);
            $user->setProvider(UserTypeEnum::OIDC_INTERNAL->value);
            $user->setCreatedBy(self::BLAMABLE_IDENTIFIER);
        }
        // Update/set user properties
        $user->setFullName($name);
        $user->setRoles($roles);

        return $user;
    }

    private function getNormalizedRoles(array $roles): array
    {
        $normalizedRoles = [];

        foreach ($roles as $role) {
            $normalizedRoles[] = $this->normalizeRoleName($role);
        }

        return $normalizedRoles;
    }

    private function normalizeRoleName(string $role): string
    {
        return match ($role) {
            'superadministrator' => 'ROLE_SUPER_ADMIN',
            'administrator' => 'ROLE_ADMIN',
            'redaktør' => 'ROLE_EDITOR',
            default => 'ROLE_USER',
        };
    }
}
