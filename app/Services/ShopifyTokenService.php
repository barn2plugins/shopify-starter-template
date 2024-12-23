<?php

namespace Barn2App\Services;

use Barn2App\Exceptions\InvalidTokenException;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\UnencryptedToken;

class ShopifyTokenService
{
    /**
     * Decode a Shopify JWT token
     *
     * @params string $token
     */
    public function decodeToken(string $token): array
    {
        try {
            $parser      = new Parser(new JoseEncoder);
            $parsedToken = $parser->parse($token);

            $claims = $parsedToken->claims()->all();

            assert($parsedToken instanceof UnencryptedToken);

            return [
                'host' => $claims['host'] ?? null,
                'shop' => $this->extractShopDomain($claims['dest'] ?? null),
            ];
        } catch (\Exception $e) {
            throw new InvalidTokenException('Failed to decode the token: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Extract shop domain from destination URL
     */
    private function extractShopDomain(?string $destUrl): ?string
    {
        return $destUrl ? parse_url($destUrl, PHP_URL_HOST) : null;
    }
}
