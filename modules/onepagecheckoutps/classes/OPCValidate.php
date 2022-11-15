<?php
/**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * @author    PresTeamShop SAS (Registered Trademark) <info@presteamshop.com>
 * @copyright 2011-2022 PresTeamShop SAS, All rights reserved.
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * @category  PrestaShop
 * @category  Module
 */

class OPCValidate
{
    public static function isValidRUTChile($rut)
    {
        if (!preg_match("/^[0-9.]+[-]?+[0-9kK]{1}/", $rut)) {
            return false;
        }

        $rut = preg_replace('/[\.\-]/i', '', $rut);
        $dv = Tools::substr($rut, -1);
        $numero = Tools::substr($rut, 0, Tools::strlen($rut) - 1);
        $i = 2;
        $suma = 0;

        foreach (array_reverse(str_split($numero)) as $v) {
            if ($i == 8) {
                $i = 2;
            }
            $suma += $v * $i;
            ++$i;
        }

        $dvr = 11 - ($suma % 11);

        if ($dvr == 11) {
            $dvr = 0;
        }
        if ($dvr == 10) {
            $dvr = 'K';
        }
        if ($dvr == Tools::strtoupper($dv)) {
            return true;
        } else {
            return false;
        }
    }

    public static function isValidRUCEcuador($ruc)
    {
        if (Tools::strlen((string)$ruc) > 13) {
            return false;
            //You must enter maximum 13 characters.
        }

        if (Tools::strlen((string)$ruc) < 10) {
            return false;
            //You must enter minimun 10 characters.
        }

//        $VP_dig_verif = 0;
        $VP_digito1   = 0;
        $VP_digito2   = 0;
        $VP_digito3   = 0;
        $VP_digito4   = 0;
        $VP_digito5   = 0;
        $VP_digito6   = 0;
        $VP_digito7   = 0;
        $VP_digito8   = 0;
        $VP_digito9   = 0;
        $VP_digito10  = 0;
        $VP_calc1     = 0;
        $VP_calc3     = 0;
        $VP_calc5     = 0;
        $VP_calc7     = 0;
        $VP_calc9     = 0;
        $VP_sumac     = 0;
        $VP_suma6     = 0;
        $VP_suma9     = 0;
//        $VP_division  = 0;
        $VP_residuo   = 0;
        $VP_operacion = 0;
        $VP_sw        = 0;
        $VP_3er_dig   = 0;
        $VP_CANT      = 0;

//        $VP_dig_verif = Tools::substr($ruc, 9, 1);

        /* EMPIEZA DESDE AQUI LA VALIDACION DEL SRI SEGUN DOCUMENTO
          VALIDO QUE NO SE INGRESEN LETRAS EN EL CAMPO DE LA CEDULA */

        if (!is_numeric($ruc)) {
            return false;
            //You must enter only numerical characters in the RUC/ID.
        } else {
            $VP_digito1   = (int)Tools::substr(trim($ruc), 0, 1); /* VALIDACION DE NUMERO DE CEDULA */
            $VP_digito2   = (int)Tools::substr(trim($ruc), 1, 1);
            $VP_digito3   = (int)Tools::substr(trim($ruc), 2, 1);
            $VP_digito4   = (int)Tools::substr(trim($ruc), 3, 1);
            $VP_digito5   = (int)Tools::substr(trim($ruc), 4, 1);
            $VP_digito6   = (int)Tools::substr(trim($ruc), 5, 1);
            $VP_digito7   = (int)Tools::substr(trim($ruc), 6, 1);
            $VP_digito8   = (int)Tools::substr(trim($ruc), 7, 1);
            $VP_digito9   = (int)Tools::substr(trim($ruc), 8, 1);
            $VP_digito10  = (int)Tools::substr(trim($ruc), 9, 1);
            $VP_calc1     = 0;
            $VP_calc3     = 0;
            $VP_calc5     = 0;
            $VP_calc7     = 0;
            $VP_calc9     = 0;
            $VP_sumac     = 0;
            $VP_suma6     = 0;
            $VP_suma9     = 0;
            $VP_division  = 0;
            $VP_residuo   = 0;
            $VP_operacion = 0;
            $VP_3er_dig   = $VP_digito3;

            $VP_division = $VP_division;

            /* ---- valido que los 2 primeros digitos sean provincias existentes
              ---- valido que tercer digito este correcto */
            $nums = array(0, 1, 2, 3, 4, 5, 6, 9);

            if (in_array($VP_3er_dig, $nums)) {
                $VP_CANT = 0;
            } else {
                $VP_CANT = 1;
            }

            if ($VP_CANT === 1) {
                return false;
                //Check incorrect RUC/ID number in third digit valid.
            }

            if ((Tools::strlen($ruc) === 10 || Tools::strlen($ruc) === 13) && !($VP_3er_dig === 9)) {
                /* ----CUANDO ES CEDULA O RUC Y EL TERCER DIGITO NO ES 9 */
                $VP_sw      = 1;
                $VP_digito1 = $VP_digito1 * 2;
                $VP_digito3 = $VP_digito3 * 2;
                $VP_digito5 = $VP_digito5 * 2;
                $VP_digito7 = $VP_digito7 * 2;
                $VP_digito9 = $VP_digito9 * 2;

                if ($VP_3er_dig === 6) {
                    $VP_sw = 0;
                }

                if ($VP_digito1 >= 10) {
                    $VP_calc1 = (int)(Tools::substr((string)$VP_digito1, 0, 1))
                        + (int)(Tools::substr((string)$VP_digito1, 1, 1));
                } else {
                    $VP_calc1 = $VP_digito1;
                }

                if ($VP_digito3 >= 10) {
                    $VP_calc3 = (int)(Tools::substr((string)$VP_digito3, 0, 1))
                        + (int)(Tools::substr((string)$VP_digito3, 1, 1));
                } else {
                    $VP_calc3 = $VP_digito3;
                }

                if ($VP_digito5 >= 10) {
                    $VP_calc5 = (int)(Tools::substr((string)$VP_digito5, 0, 1))
                        + (int)(Tools::substr((string)$VP_digito5, 1, 1));
                } else {
                    $VP_calc5 = $VP_digito5;
                }

                if ($VP_digito7 >= 10) {
                    $VP_calc7 = (int)(Tools::substr((string)$VP_digito7, 0, 1))
                        + (int)(Tools::substr((string)$VP_digito7, 1, 1));
                } else {
                    $VP_calc7 = $VP_digito7;
                }

                if ($VP_digito9 >= 10) {
                    $VP_calc9 = (int)(Tools::substr((string)$VP_digito9, 0, 1))
                        + (int)(Tools::substr((string)$VP_digito9, 1, 1));
                } else {
                    $VP_calc9 = $VP_digito9;
                }

                $VP_sumac = $VP_calc1 + $VP_digito2 + $VP_calc3 + $VP_digito4
                    + $VP_calc5 + $VP_digito6 + $VP_calc7 + $VP_digito8 + $VP_calc9;

                $VP_division = $VP_sumac / 10;
                $VP_residuo  = $VP_sumac % 10;

                if ($VP_residuo === 0) {
                    if ($VP_residuo !== $VP_digito10) {
                        return false;
                        //Check incorrect ID number in verifying digit.
                    }
                }

                if ($VP_residuo > 0) {
                    $VP_operacion = 10 - $VP_residuo;
                    if ($VP_operacion !== $VP_digito10) {
                        return false;
                        //Check incorrect ID number in verifying digit.
                    }
                }
            }

            if ($VP_3er_dig === 9 && Tools::strlen($ruc) !== 13) {
                return false;
                //According to the characteristics of this identification should be RUC, verify.
            }

            /* -- CUANDO ES RUC Y ES RUC INSTITUCION PUBLICO SE VERifICA EL TERCER DIGITO */
            if (Tools::strlen($ruc) === 13 && $VP_3er_dig === 6 && $VP_sw === 0) {
                $VP_sw              = 1;
                $VP_digito1         = (int)(Tools::substr(trim($ruc), 0, 1));
                $VP_digito2         = (int)(Tools::substr(trim($ruc), 1, 1));
                $VP_digito3         = (int)(Tools::substr(trim($ruc), 2, 1));
                $VP_digito4         = (int)(Tools::substr(trim($ruc), 3, 1));
                $VP_digito5         = (int)(Tools::substr(trim($ruc), 4, 1));
                $VP_digito6         = (int)(Tools::substr(trim($ruc), 5, 1));
                $VP_digito7         = (int)(Tools::substr(trim($ruc), 6, 1));
                $VP_digito8         = (int)(Tools::substr(trim($ruc), 7, 1));
                $VP_digito9         = (int)(Tools::substr(trim($ruc), 8, 1));
                $VP_digito10        = (int)(Tools::substr(trim($ruc), 9, 1));
                $VP_division        = 0;
                $VP_residuo         = 0;

                $VP_digito1 = $VP_digito1 * 3;
                $VP_digito2 = $VP_digito2 * 2;
                $VP_digito3 = $VP_digito3 * 7;
                $VP_digito4 = $VP_digito4 * 6;
                $VP_digito5 = $VP_digito5 * 5;
                $VP_digito6 = $VP_digito6 * 4;
                $VP_digito7 = $VP_digito7 * 3;
                $VP_digito8 = $VP_digito8 * 2;

                $VP_suma6    = $VP_digito1 + $VP_digito2 + $VP_digito3 + $VP_digito4
                    + $VP_digito5 + $VP_digito6 + $VP_digito7 + $VP_digito8;
                $VP_division = $VP_suma6 / 11;
                $VP_residuo  = $VP_suma6 % 11;

                if ($VP_residuo === 0) {
                    if ($VP_residuo !== $VP_digito9) {
                        return false;
                        //Check incorrect RUC number in verifying digit.
                    }
                }

                if ($VP_residuo > 0) {
                    $VP_operacion = 11 - $VP_residuo;
                    if ($VP_operacion !== $VP_digito9) {
                        return false;
                        //Check incorrect RUC number in verifying digit.
                    }
                }
            }

            if (Tools::strlen($ruc) === 13 && $VP_3er_dig === 9 && $VP_sw === 0) {
                $VP_sw      = 1;
                $VP_digito1 = $VP_digito1 * 4;
                $VP_digito2 = $VP_digito2 * 3;
                $VP_digito3 = $VP_digito3 * 2;
                $VP_digito4 = $VP_digito4 * 7;
                $VP_digito5 = $VP_digito5 * 6;
                $VP_digito6 = $VP_digito6 * 5;
                $VP_digito7 = $VP_digito7 * 4;
                $VP_digito8 = $VP_digito8 * 3;
                $VP_digito9 = $VP_digito9 * 2;

                $VP_suma9    = $VP_digito1 + $VP_digito2 + $VP_digito3 + $VP_digito4 + $VP_digito5 + $VP_digito6
                    + $VP_digito7 + $VP_digito8 + $VP_digito9;
                $VP_division = $VP_suma9 / 11;
                $VP_residuo  = $VP_suma9 % 11;

                if ($VP_residuo === 0) {
                    if ($VP_residuo !== $VP_digito10) {
                        return false;
                        //Check incorrect RUC number in verifying digit.
                    }
                }

                if ($VP_residuo > 0) {
                    $VP_operacion = 11 - $VP_residuo;
                    if ($VP_operacion !== $VP_digito10) {
                        return false;
                        //Check incorrect RUC number in verifying digit.
                    }
                }
            }
        }

        return true;
    }

    public static function isValidNIFSpain($nif)
    {
        require_once dirname(__FILE__).'/../lib/nif-nie-cif.php';

        if (isValidNIF($nif)) {
            return true;
        }

        return false;
    }

    public static function isValidNIFSpainOnly($nif)
    {
        require_once dirname(__FILE__).'/../lib/nif-nie-cif.php';

        if (isValidNIF($nif)) {
            return true;
        }

        return false;
    }
}
