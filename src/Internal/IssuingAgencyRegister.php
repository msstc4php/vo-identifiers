<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Internal;

/**
 * Issuing agency codes of ISO/IEC 15459, code to agency name only, from the Register of Issuing Agency Codes version
 * 2026-06-16 (CEN/ISO/AIM, section 2, ordered by code). Contact details are not carried over. The codes `0` to `9` are one
 * entry of the register ("0 thru 9", GS1 Global Office). No code of this edition is the beginning of another, so the
 * longest match from the start of an identifier is the only match.
 *
 * @internal
 *
 * @see https://www.aimglobal.org/wp-content/uploads/2026/06/Register-IAC-15459-2-20260616.pdf Register of Issuing Agency Codes for ISO/IEC 15459
 */
final class IssuingAgencyRegister
{
    /** @var array<int|string, non-empty-string> */
    private const array AGENCIES = [
        '0' => 'GS1 Global Office', '1' => 'GS1 Global Office', '2' => 'GS1 Global Office', '3' => 'GS1 Global Office',
        '4' => 'GS1 Global Office', '5' => 'GS1 Global Office', '6' => 'GS1 Global Office', '7' => 'GS1 Global Office',
        '8' => 'GS1 Global Office', '9' => 'GS1 Global Office',
        'D' => 'NATO Support and Procurement Agency (NSPA)',
        'GH' => 'Ghana Revenue Authority',
        'J' => 'Universal Postal Union',
        'KCA' => 'Department of National Defense - Canada',
        'KDK' => 'DALO (Danish Defense Acquisition and Logistics Organization)',
        'KFR' => 'French National Secure Documents Agency (Agence Nationale des Titres sécurisés - ANTS)',
        'KKR' => 'Korea Institute of Distribution and Logistics (KIDL)',
        'KLT' => 'State Tax Inspectorate of Lithuania',
        'KPL' => 'PWPW S.A. (Polish Security Printing Works)',
        'KNL' => 'Ministerie van Defensie',
        'KRU' => 'FSBI <<46 CRI RFMD>>',
        'KSE' => 'Försvarets Materielverk',
        'KUS' => 'TCJ5/4-I (United States Transportation Command)',
        'KXM' => 'NATO Support and Procurement Agency (NSPA)',
        'LA' => 'JIPDEC',
        'LB' => 'iconectiv',
        'LD' => 'DOD-DLIS (Department of Defense - Defense Logistics Information Service)',
        'LE' => 'EDIFICE (The Global Network for B2B Integration in High Tech Industries)',
        'LF' => 'FIATA (International Federation of Freight Forwarders Associations)',
        'LH' => 'HIBCC (Health Industry Business Communications Council)',
        'LI' => 'ICCBBA (International Council for Commonality in Blood Bank Automation Inc.)',
        'LM' => 'Telefonaktiebolaget LM Ericsson',
        'LN' => 'ABOL SOFTWARE INC.',
        'MA' => 'ZIIOT (Zhongguancun Industry & Information Research Institute of Two-Dimensional Code Technology)',
        'ND' => 'DHL Freight GmbH',
        'NL' => 'PostNL',
        'OD' => 'ODETTE International Limited',
        'PP' => 'Informationsstelle für Arznei. IFA GmbH',
        'QC' => 'Eurodata Council',
        'RG' => 'Xifrat Daten AG',
        'RH' => 'HIBCC (Health Industry Business Comm. Council)',
        'SI' => 'SIEMENS AG',
        'ST' => 'EUROFER (European Confederation of Iron and Steel Industries)',
        'TAJ' => 'National Tax Agency Japan',
        'UN' => 'Dun & Bradstreet',
        'VAA' => 'China Academy of Information & Communications Technology (CAICT)',
        'VBS' => 'Bosch und Siemens Hausgeräte GmbH',
        'VDA' => 'Verband der Automobilindustrie e. V (VDA)/German Association of the Automotive Industry',
        'VDS' => 'VDSIC (Visible Digital Seal International Council)',
        'VEC' => 'ECRI Institute',
        'VEG' => 'Unify GmbH & Co. KG (formerly Siemens Enterprise Communications)',
        'VFS' => 'Federal State Unitary Enterprise "NIISU"',
        'VGL' => 'DHL Express Benelux',
        'VGT' => 'GTF (Group of Terrestrial Freight Forwarders)',
        'VIB' => 'IBM (International Business Machines)',
        'VIE' => 'IEEE',
        'VIP' => 'C.I.P. (Club Inter Pharmaceutique)',
        'VTA' => 'Global Textile Scheme GmbH',
        'VTD' => 'Teikoku Databank, Ltd. (TDB)',
        'WAA' => 'International Tax Stamp Association',
        'XID' => 'DOI Foundation',
        'XRA' => 'RAIN Alliance, Inc.',
    ];

    /**
     * @return non-empty-string|null the registered code the value starts with, null when none does
     */
    public static function codeOf(string $identifier): ?string
    {
        foreach ([3, 2, 1] as $length) {
            $code = \substr($identifier, 0, $length);
            if (isset(self::AGENCIES[$code])) {
                return $code;
            }
        }

        return null;
    }

    /**
     * @return non-empty-string|null null for a code that is not registered
     */
    public static function nameOf(string $code): ?string
    {
        return self::AGENCIES[$code] ?? null;
    }

    /**
     * @return list<string>
     */
    public static function codes(): array
    {
        return \array_map(strval(...), \array_keys(self::AGENCIES));
    }
}
