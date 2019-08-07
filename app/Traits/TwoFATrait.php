<?php

namespace App\Traits;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Exceptions\InsecureCallException;
use PragmaRX\Google2FA\Google2FA;

/**
 * Trait TwoFATrait
 * @package App\Traits
 */
trait TwoFATrait
{
    /**
     * @return $this
     */
    public function enable2FA()
    {
        $this->is_2fa_enabled = true;
        $this->save();

        $this->generate2FASecretKey();

        return $this;
    }

    /**
     * @return $this
     */
    public function disable2FA()
    {
        $this->is_2fa_enabled = false;
        $this->google_2fa_secret = null;
        $this->save();

        return $this;
    }

    /**
     * @return $this
     */
    public function generate2FASecretKey()
    {
        $google2fa = new Google2FA();

        try {
            $this->google_2fa_secret = $google2fa->generateSecretKey();
            $this->save();
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }

        return $this;
    }

    /**
     * @return string
     */
    public function get2FACode(): string
    {
        $google2fa = new Google2FA();
        $google2fa->setAllowInsecureCallToGoogleApis(true);

        if (!$this->is_2fa_enabled) {
            return abort(400, '2FA is not enabled');
        }

        try {
            return $google2fa->getQRCodeGoogleUrl(
                config('coffective.2fa_company_name'),
                $this->email,
                $this->google_2fa_secret
            );
        } catch (InsecureCallException $e) {
            return abort(500, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return bool|int|void
     */
    public function verify2FASecret(Request $request)
    {
        $request->validate([
            'secret' => 'required',
        ]);

        $google2fa = new Google2FA();

        $secret = $request->input('secret');

        try {
            return $google2fa->verifyKey($this->google_2fa_secret, $secret);
        } catch (\Exception $e) {
            return abort(500, $e->getMessage());
        }
    }
}
