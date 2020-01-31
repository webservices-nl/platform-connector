<?php

namespace WebservicesNl\Platform\Webservices;

use WebservicesNl\Common\Exception\Server\Data\NotFoundException;
use WebservicesNl\Connector\AbstractConnector;

/**
 * Class Connector.
 *
 * Proxy for calling everything Webservices regardless of the underlying protocol.
 * All webservice related functions are mapped here. Note that for BC reasons old deprecated functions are listed here
 * just as well. When possible please use the latest version.
 *
 * @link https://webview.webservices.nl/documentation
 * @codeCoverageIgnore This is a silly proxy class
 */
class Connector extends AbstractConnector
{
    const PLATFORM_NAME = 'webservices';

    /**
     * Set host restrictions for the account.
     *
     * @param int    $accountId    ID of the account, use 0 for the current user's account
     * @param string $restrictions A string with host restrictions separated by semi colons (;)
     *
     * @return \stdClass
     */
    public function accountEditHostRestrictions($accountId, $restrictions)
    {
        return $this->getAdapter()->call(
            'accountEditHostRestrictions',
            ['accountid' => $accountId, 'restrictions' => $restrictions]
        );
    }

    /**
     * Edit the properties (<AccountV2>) of an account.
     * This method allows <Group::Account admins> to edit their account profile.
     *
     * @param int    $accountId        Account ID of the account to edit, use 0 for the current user's account
     * @param string $address          address of the company using this account
     * @param string $contactName      name of the contact person responsible for this account
     * @param string $contactEmail     email address of the contact person responsible for this account
     * @param string $telephone        telephone number of the contact person responsible for this account
     * @param string $fax              fax number of the contact person responsible for this account
     * @param string $description      description of the account to its users
     * @param float  $balanceThreshold balance threshold to alert account, use 0 to disable
     *
     * @return \stdClass
     */
    public function accountEditV2(
        $accountId,
        $address,
        $contactName,
        $contactEmail,
        $telephone,
        $fax,
        $description,
        $balanceThreshold
    ) {
        return $this->getAdapter()->call(
            'accountEditV2',
            [
                'accountid' => $accountId,
                'address' => $address,
                'contactname' => $contactName,
                'contactemail' => $contactEmail,
                'telephone' => $telephone,
                'fax' => $fax,
                'description' => $description,
                'balancethreshold' => (float) $balanceThreshold,
            ]
        );
    }

    /**
     * Get the id of an account created with a token from <accountGetCreationToken>.
     * Depending on the outcome of the account registration the following is returned:
     * - A value greater than 0 - The customer has successfully registered an account. The returned value is the
     *                            accountId.
     * - A value of 0           - The customer has not yet finished the account registration process. It may be that
     *                            Webservices.nl is awaiting confirmation of a payment performed by the customer.
     *                            You should *not* retrieve a new account registration token, or direct the customer to
     *                            the account registration page again. This could result in the customer registering
     *                            and paying for an account that is never used. Instead, try calling
     *                            <accountGetCreationStatus> again later.
     * - A 'Server.Data.NotFound' error - This error indicates that the registration process was unsuccesful.
     *                            See <Error Handling::Error codes>. You may start the registration process over by
     *                            calling <accountGetCreationToken>.
     *
     * @param string $token a token retrieved using <accountGetCreationToken>
     *
     * @throws NotFoundException
     *
     * @return int accountId The account id, which is 0 when the account registration has not finished yet
     */
    public function accountGetCreationStatus($token)
    {
        return $this->getAdapter()->call('accountGetCreationStatus', ['token' => $token]);
    }

    /**
     * Retrieve a token with which a new account may be registered via the <Webview Interface> by one of your customers.
     * The newly created account will be associated with your account. Tokens are only valid for a limited amount of
     * time. Use <accountGetCreationStatus> to get the id of the account created using the token. If a customer arrives
     * at this URL the <accountGetCreationStatus> should be called to check if account creation was successful.
     *
     * @param string $returnUrl the URL to which the customer is redirected after registering a Webservices.nl account
     *
     * @return string AccountCreationToken
     */
    public function accountGetCreationToken($returnUrl)
    {
        return $this->getAdapter()->call('accountGetCreationToken', ['return_url' => $returnUrl]);
    }

    /**
     * Retrieve a token that can be used order account balance via the <Webview Interface>.
     *
     * @param int    $accountId id of the account for which balance will be ordered
     * @param string $returnUrl the URL to which the customer is redirected after finishing the order process
     *
     * @return string
     */
    public function accountGetOrderToken($accountId, $returnUrl)
    {
        return $this->getAdapter()->call(
            'accountGetOrderToken',
            ['accountid' => $accountId, 'return_url' => $returnUrl]
        );
    }

    /**
     * List all users in this account. This method is only available to <Group::Account admins>.
     *
     * @param int $accountId ID of the account to list, use 0 for the current user's account
     * @param int $page      Page to retrieve, pages start counting at 1
     *
     * @return \stdClass <Patterns::{Type}PagedResult> of <UserV2> entries
     */
    public function accountUserListV2($accountId, $page)
    {
        return $this->getAdapter()->call('accountUserListV2', ['accountid' => (int) $accountId, 'page' => $page]);
    }

    /**
     * Search for users of an account using a search phrase.
     * This method is only available to <Group::Account admins>.
     *
     * @param int    $accountId ID of the account to list, use 0 for the current user's account
     * @param string $phrase    Phrase to search for in user profiles
     * @param int    $page      Page to retrieve, pages start counting at 1
     *
     * @return \stdClass userV2 entries
     */
    public function accountUserSearchV2($accountId, $phrase, $page)
    {
        return $this->getAdapter()->call(
            'accountUserSearchV2',
            ['accountid' => $accountId, 'phrase' => $phrase, 'page' => $page]
        );
    }

    /**
     * Returns the accounts balance.
     *
     * @param int $accountId ID of the account to view the balance of, use 0 for the current account
     *
     * @link https://webview.webservices.nl/documentation/files/service_accounting-php.html#Accounting.accountViewBalance
     *
     * @return int
     */
    public function accountViewBalance($accountId = 0)
    {
        return $this->getAdapter()->call('accountViewBalance', ['accountid' => $accountId]);
    }

    /**
     * View host restrictions for the account.
     *
     * @param int $accountId ID of the account, use 0 for the current user's account
     *
     * @link https://webview.webservices.nl/documentation/files/service_accounting-php.html#Accounting.accountViewHostRestrictions
     *
     * @return string containing all restrictions, separated by semicolons
     */
    public function accountViewHostRestrictions($accountId)
    {
        return $this->getAdapter()->call('accountViewHostRestrictions', ['accountid' => $accountId]);
    }

    /**
     * View the profile of an account.
     *
     * @param int $accountId Account ID of the account to move use 0 for the account
     *
     * @link https://webview.webservices.nl/documentation/files/service_accounting-php.html#Accounting.accountViewV2
     *
     * @return \stdClass
     */
    public function accountViewV2($accountId)
    {
        return $this->getAdapter()->call('accountViewV2', ['accountid' => (int) $accountId]);
    }

    /**
     * Returns a list of all neighborhood codes in the city.
     *
     * @param string $name    Name or identifier of the city
     * @param bool   $postbus indicating whether Postbus neighborhood codes should be included in the result
     * @param int    $page    Page to retrieve, pages start counting at 1
     *
     * @link https://webview.webservices.nl/documentation/files/service_address-php.html#Address.addressCityListNeighborhoods
     *
     * @return \stdClass <Patterns::{Type}PagedResult>
     */
    public function addressCityListNeighborhoods($name, $postbus, $page)
    {
        return $this->getAdapter()->call(
            'addressCityListNeighborhoods',
            ['name' => $name, 'postbus' => (bool) $postbus, 'page' => $page]
        );
    }

    /**
     * Search for all cities that match a phrase.
     * Cities are also matched if input matches a commonly used alternative city name. Exact matches on the official
     * name are listed first, the rest of the results are sorted alphabetically. This method differs from
     * addressCitySearch by returning <CityV2> entries instead of <City> entries, thus giving more information about a
     * city.
     *
     * @param string $name phrase to search cities for, or the numeric identifier for the city
     * @param int    $page Page to retrieve, pages start counting at 1
     *
     * @link http://webview.webservices.nl/documentation/files/service_address-php.html#Address.addressCitySearchV2
     *
     * @return \stdClass <Patterns::{Type}PagedResult> of <CityV2> entries
     */
    public function addressCitySearchV2($name, $page)
    {
        return $this->getAdapter()->call('addressCitySearchV2', ['name' => $name, 'page' => $page]);
    }

    /**
     * List all cities in specific municipalities.
     *
     * @param string $name search municipalities for, or the numeric identifier for the municipality
     * @param int    $page Page to retrieve, pages start counting at 1
     *
     * @link https://webview.webservices.nl/documentation/files/service_address-php.html#Address.addressDistrictListCities
     *
     * @return \stdClass <Patterns::{Type}PagedResult>
     */
    public function addressDistrictListCities($name, $page)
    {
        return $this->getAdapter()->call('addressDistrictListCities', ['name' => $name, 'page' => $page]);
    }

    /**
     * Search for all municipalities that match a phrase.
     *
     * @param string $name phrase to search municipalities for, or the numeric identifier for the municipality
     * @param string $page Page to retrieve, pages start counting at 1
     *
     * @link https://webview.webservices.nl/documentation/files/service_address-php.html#Address.addressDistrictSearch
     *
     * @return \stdClass <Patterns::{Type}PagedResult> of <District> entries
     */
    public function addressDistrictSearch($name, $page)
    {
        return $this->getAdapter()->call('addressDistrictSearch', ['name' => $name, 'page' => $page]);
    }

    /**
     * Search for addresses in the <Perceel> format, using a single search phrase.
     * The phrase can be a partial address. To search using separate fields for each address part, use
     * <addressPerceelFullParameterSearchV2>.
     *
     * @param string $province          Phrase used to select the province of the address, see <Perceel>.provincienaam
     * @param string $district          Phrase used to select the municipality of the address
     * @param string $city              Phrase used to select the city of the address
     * @param string $street            Phrase used to select the street of the address
     * @param int    $houseNo           Number used to select the house number of the address
     * @param string $houseNoAddition   Phrase used to select the house number addition of the address
     * @param string $nbCode            number used to select the neighborhoodcode of the address, the first four
     *                                  numbers of the postcode
     * @param string $letterCombination Phrase used to select the lettercombination of the address, the last two
     *                                  letters of the postcode. See <Perceel>.lettercombinatie
     * @param string $addressType       Phrase used to select the addresstype of the address
     * @param int    $page              Page to retrieve, pages start counting at 1
     *
     * @deprecated please use addressPerceelFullParameterSearchV2
     * @link       https://webview.webservices.nl/documentation/files/service_address-php.html#Address.addressPerceelFullParameterSearch
     *
     * @return \stdClass
     */
    public function addressPerceelFullParameterSearch(
        $province,
        $district,
        $city,
        $street,
        $houseNo,
        $houseNoAddition,
        $nbCode,
        $letterCombination,
        $addressType,
        $page
    ) {
        return $this->getAdapter()->call(
            'addressPerceelFullParameterSearch',
            [
                'province' => $province,
                'district' => $district,
                'city' => $city,
                'street' => $street,
                'houseNo' => $houseNo,
                'houseNoAddition' => $houseNoAddition,
                'nbcode' => $nbCode,
                'lettercombination' => $letterCombination,
                'addresstype' => $addressType,
                'page' => $page,
            ]
        );
    }

    /**
     * Search for addresses in the <Perceel> format, using different search phrases for each address part.
     * PO box matches:
     * See <Perceel> for information on how PO box matches are returned.
     *
     * @param string $province          Phrase used to select the province of the address, see <Perceel>.provincienaam
     * @param string $district          Phrase used to select the municipality of the address
     * @param string $city              Phrase used to select the city of the address
     * @param string $street            Phrase used to select the street of the address
     * @param int    $houseNo           Number used to select the house number of the address
     * @param string $houseNoAddition   Phrase used to select the house number addition of the address
     * @param string $nbCode            number used to select the neighborhoodcode of the address, the first four
     *                                  numbers of the postcode
     * @param string $letterCombination Phrase used to select the lettercombination of the address, the last two
     *                                  letters of the postcode. See <Perceel>.lettercombinatie
     * @param string $addresstype       Phrase used to select the addresstype of the address
     * @param int    $page              Page to retrieve, pages start counting at 1
     *
     * @return \stdClass <PerceelSearchPartsPagedResult>
     */
    public function addressPerceelFullParameterSearchV2(
        $province,
        $district,
        $city,
        $street,
        $houseNo,
        $houseNoAddition,
        $nbCode,
        $letterCombination,
        $addresstype,
        $page
    ) {
        return $this->getAdapter()->call(
            'addressPerceelFullParameterSearchV2',
            [
                'province' => $province,
                'district' => $district,
                'city' => $city,
                'street' => $street,
                'houseNo' => $houseNo,
                'houseNoAddition' => $houseNoAddition,
                'nbcode' => $nbCode,
                'lettercombination' => $letterCombination,
                'addresstype' => $addresstype,
                'page' => $page,
            ]
        );
    }

    /**
     * Search for addresses in the <Perceel> format, using different search phrases for each address part.
     *
     * @param string $province        Phrase used to select the province of the address, see <Perceel>.provincienaam
     * @param string $district        Phrase used to select the municipality of the address
     * @param string $city            Phrase used to select the city of the address
     * @param string $street          Phrase used to select the street of the address
     * @param int    $houseNo         Number used to select the house number of the address
     * @param string $houseNoAddition Phrase used to select the house number addition of the address
     * @param int    $page
     *
     * @deprecated please use addressPerceelFullParameterSearchV2
     *
     * @return \stdClass
     */
    public function addressPerceelParameterSearch(
        $province,
        $district,
        $city,
        $street,
        $houseNo,
        $houseNoAddition,
        $page
    ) {
        return $this->getAdapter()->call(
            'addressPerceelParameterSearch',
            [
                'province' => $province,
                'district' => $district,
                'city' => $city,
                'street' => $street,
                'houseNo' => $houseNo,
                'houseNoAddition' => $houseNoAddition,
                'page' => $page,
            ]
        );
    }

    /**
     * Search for addresses in the <Perceel> format, using a single search phrase.
     * The phrase can be a partial address.
     * Supported phrases:
     * postcode, house number - 1188 VP, 202bis
     * postcode - 1188 VP
     * neighborhood code - 1188
     * city, address - Amstelveen, Amsteldijk Zuid 202bis
     * address, city - Amsteldijk Zuid 202bis, Amstelveen
     * city, street - Amstelveen, Amsteldijk Zuid
     * address - Amsteldijk Zuid 202bis.
     *
     * @param string $address Address phrase to search for in addresses
     * @param int    $page    Page to retrieve, pages start counting at 1
     *
     * @return \stdClass <PerceelSearchPartsPagedResult>
     */
    public function addressPerceelPhraseSearch($address, $page)
    {
        return $this->getAdapter()->call('addressPerceelPhraseSearch', ['address' => $address, 'page' => $page]);
    }

    /**
     * List all provinces.
     *
     * @param int $page
     *
     * @return \stdClass
     */
    public function addressProvinceList($page)
    {
        return $this->getAdapter()->call('addressProvinceList', ['page' => $page]);
    }

    /**
     * List all municipalities in a specific provinces.
     *
     * @param string $name Name or code of the province to list the municipalities from
     * @param int    $page Page to retrieve, pages start counting at 1
     *
     * @return \stdClass of <District> entries
     */
    public function addressProvinceListDistricts($name, $page)
    {
        return $this->getAdapter()->call('addressProvinceListDistricts', ['name' => $name, 'page' => $page]);
    }

    /**
     * Returns a list of all neighborhood codes in the province.
     *
     * @param string $name    Name or code of the province
     * @param bool   $postbus Boolean indicating whether Postbus neighborhood codes should be included in the result
     * @param int    $page    Page to retrieve, pages start counting at 1
     *
     * @return \stdClass <Patterns::{Type}PagedResult> of <Neighborhood> entries
     */
    public function addressProvinceListNeighborhoods($name, $postbus, $page)
    {
        return $this->getAdapter()->call(
            'addressProvinceListNeighborhoods',
            ['name' => $name, 'postbus' => $postbus, 'page' => $page]
        );
    }

    /**
     * Search for all provinces that match a phrase.
     *
     * @param string $name phrase to search for in the province names, or the province code
     * @param int    $page Page to retrieve, pages start counting at 1
     *
     * @return \stdClass <Patterns::{Type}PagedResult> of <Province> entries
     */
    public function addressProvinceSearch($name, $page)
    {
        return $this->getAdapter()->call('addressProvinceSearch', ['name' => $name, 'page' => $page]);
    }

    /**
     * Search for a specific address.
     * Where the address contains the street, house number and house number addition concatenated. This is useful if the
     * house number is not stored separate from the street name.
     * A number of <RangeAddress> entries is returned. The street names in the result may not exactly match the street
     * in the request. To account for different writing styles and spelling errors, streets which match approximately
     * are also returned. E.g. "Calverstraat, Amsterdam" will return an address for the "Kalverstraat". The results are
     * ordered on how well they match, with the best matches first.
     * If the given house number does not exist in the postcode range, the house number field is left empty. In this
     * case, the <RangeAddress> contains a <PCReeks> which matches the street, but it contains no house number or house
     * number addition. For example, searching for "Dam 44, Amsterdam" returns the <PCReeks> for the Dam, but the result
     * omits the house number since there is no house number 44 on the Dam.
     *
     * @param string $address  Street, house number and house number addition of the searched address. Required
     * @param string $postcode Postcode in 1234AA format. Optional.
     * @param string $city     Phrase used to select the city of the address, see <PCReeks>.plaatsnaam. Optional.
     * @param int    $page     Page to retrieve, pages start counting at 1
     *
     * @return \stdClass pagedResult of <RangeAddress> entries
     */
    public function addressReeksAddressSearch($address, $postcode, $city, $page)
    {
        return $this->getAdapter()->call(
            'addressReeksAddressSearch',
            ['address' => $address, 'postcode' => $postcode, 'city' => $city, 'page' => $page]
        );
    }

    /**
     * Search for addresses in the <PCReeks> format, using different search phrases for each address part.
     *
     * @param string $province          Phrase to search for in province name, or code of the province. See
     *                                  <PCReeks>.provincienaam and <PCReeks>.provinciecode
     * @param string $district          Phrase used to select the municipality of the address, see
     *                                  <PCReeks>. gemeentenaam
     * @param string $city              Phrase used to select the city of the address, see <PCReeks>.plaatsnaam
     * @param string $street            Phrase used to select the street of the address, see <PCReeks>.straatnaam
     * @param string $houseNo           Number used to select the house number of the address, see <PCReeks>.huisnr_van
     * @param string $houseNoAddition   Phrase used to select the house number addition of the address
     * @param string $nbCode            Number used to select the neighborhoodcode of the address, the first four
     *                                  numbers of the postcode. See <PCReeks>.wijkcode
     * @param string $letterCombination Phrase used to select the lettercombination of the address, the last two
     *                                  letters of the postcode. See <PCReeks>.lettercombinatie
     * @param int    $addressType       Phrase used to select the addresstype of the address, see
     *                                  <PCReeks>.reeksindicatie
     * @param int    $page              Page to retrieve, pages start counting at 1
     *
     * @return \stdClass PCReeksSearchPartsPagedResult
     */
    public function addressReeksFullParameterSearch(
        $province,
        $district,
        $city,
        $street,
        $houseNo,
        $houseNoAddition,
        $nbCode,
        $letterCombination,
        $addressType,
        $page
    ) {
        return $this->getAdapter()->call(
            'addressReeksFullParameterSearch',
            [
                'province' => $province,
                'district' => $district,
                'city' => $city,
                'street' => $street,
                'houseNo' => $houseNo,
                'houseNoAddition' => $houseNoAddition,
                'nbcode' => $nbCode,
                'lettercombination' => $letterCombination,
                'addresstype' => $addressType,
                'page' => $page,
            ]
        );
    }

    /**
     * Search for addresses in the <PCReeks> format, using different search phrases for each address part.
     * Notice: <addressReeksFullParameterSearch> allows more parameters to search.
     *
     * @param string $province        Phrase to search for in province name, or code of the province. See
     *                                <PCReeks>.provincienaam and <PCReeks>.provinciecode
     * @param string $district        Phrase used to select the municipality of the address, see <PCReeks>.gemeentenaam
     * @param string $city            Phrase used to select the city of the address, see <PCReeks>.plaatsnaam
     * @param string $street          Phrase used to select the street of the address, see <PCReeks>.straatnaam
     * @param int    $houseNo         Number used to select the house number of the address, see <PCReeks>.huisnr_van
     * @param string $houseNoAddition Phrase used to select the house number addition of the address
     * @param int    $page            Page to retrieve, pages start counting at 1
     *
     * @return \stdClass PCReeksSearchPartsPagedResult
     */
    public function addressReeksParameterSearch($province, $district, $city, $street, $houseNo, $houseNoAddition, $page)
    {
        return $this->getAdapter()->call(
            'addressReeksParameterSearch',
            [
                'province' => $province,
                'district' => $district,
                'city' => $city,
                'street' => $street,
                'houseNo' => $houseNo,
                'houseNoAddition' => $houseNoAddition,
                'page' => $page,
            ]
        );
    }

    /**
     * Determine if a specific address exists using the unique '1234AA12' postcode + house number format.
     * It returns either the address in <PCReeks> format, or an error if no matching address exists. If you want to
     * validate an address not using this unique identifier,
     * use <addressReeksAddressSearch> or <addressReeksFullParameterSearch>.
     *
     * @param string $address - Address to validate using the unique '1234AA12' postcode + house number format
     *
     * @throws NotFoundException
     *
     * @return \stdClass <PCReeks>
     */
    public function addressReeksPostcodeSearch($address)
    {
        return $this->getAdapter()->call('addressReeksPostcodeSearch', ['address' => $address]);
    }

    /**
     * Lookup the telephone area codes related to a given neighborhood code.
     *
     * @param string $neighborhoodCode neighborhood code to lookup
     * @param int    $page             Page to retrieve, pages start counting at 1
     *
     * @link https://webview.webservices.nl/documentation/files/service_areacode-php.html
     *
     * @return \stdClass <Patterns::{Type}PagedResult> of <AreaCode>
     */
    public function areaCodeLookup($neighborhoodCode, $page)
    {
        return $this->getAdapter()->call('areaCodeLookup', ['neighborhoodcode' => $neighborhoodCode, 'page' => $page]);
    }

    /**
     * Lookup the telephone area code related to a given postcode.
     *
     * @param string $postcode postcode to lookup
     *
     * @link https://webview.webservices.nl/documentation/files/service_areacode-php.html#Areacode.areaCodePostcodeLookup
     *
     * @return \stdClass <Patterns::{Type}Array> of <AreaCode>
     */
    public function areaCodePostcodeLookup($postcode)
    {
        return $this->getAdapter()->call('areaCodePostcodeLookup', ['postcode' => $postcode]);
    }

    /**
     * Lookup the neighborhood codes related to a given telephone area code.
     *
     * @param string $areaCode Telephone areacode to lookup
     * @param int    $page     Page to retrieve
     *
     * @link https://webview.webservices.nl/documentation/files/service_areacode-php.html#Areacode.areaCodeToNeighborhoodcode
     *
     * @return \stdClass A <Patterns::{Type}PagedResult> of <Neighborhood> entries
     */
    public function areaCodeToNeighborhoodcode($areaCode, $page)
    {
        return $this->getAdapter()->call('areaCodeToNeighborhoodcode', ['areacode' => $areaCode, 'page' => $page]);
    }

    /**
     * Retrieve a Bovag member using a Bovag identifier.
     *
     * @param string $bovagId The identifier used by Bovag to identify a member
     *
     * @link https://webview.webservices.nl/documentation/files/service_bovag-php.html#Bovag.bovagGetMemberByBovagId
     *
     * @return \stdClass <BovagMember>
     */
    public function bovagGetMemberByBovagId($bovagId)
    {
        return $this->getAdapter()->call('bovagGetMemberByBovagId', ['bovag_id' => $bovagId]);
    }

    /**
     * Retrieve a Bovag member using a DutchBusiness reference.
     *
     * @param string $dossierNumber       Chamber of Commerce number
     * @param string $establishmentNumber Establishment number
     *
     * @link https://webview.webservices.nl/documentation/files/service_bovag-php.html#Bovag.bovagGetMemberByDutchBusiness
     *
     * @return \stdClass <BovagMember>
     */
    public function bovagGetMemberByDutchBusiness($dossierNumber, $establishmentNumber)
    {
        return $this->getAdapter()->call(
            'bovagGetMemberByDutchBusiness',
            ['dossier_number' => $dossierNumber, 'establishment_number' => $establishmentNumber]
        );
    }

    /**
     * Retrieve Auto disk price information.
     * Autodisk data is available for yellow license plates younger than 2004. Coverage for older plates is very
     * limited.
     *
     * @param string $licensePlate Dutch license plate (kenteken)
     *
     * @link https://webview.webservices.nl/documentation/files/service_car-php.html#Car.carATDPrice
     *
     * @return \stdClass A <CarATDPrices>
     */
    public function carATDPrice($licensePlate)
    {
        return $this->getAdapter()->call('carATDPrice', ['license_plate' => $licensePlate]);
    }

    /**
     * Check the validity of a license plate and check code ('meldcode') combination.
     * This method differs from <carVWEMeldcodeCheck> in that it also returns whether a car is active.
     *
     * @param string $licensePlate Dutch license plate (kenteken)
     * @param string $code         code (meldcode), 4 digits
     *
     * @link https://webview.webservices.nl/documentation/files/service_car-php.html#Car.carRDWCarCheckCode
     *
     * @return \stdClass <CarCheckCode>.
     */
    public function carRDWCarCheckCode($licensePlate, $code)
    {
        return $this->getAdapter()->call(
            'carRDWCarCheckCode',
            ['license_plate' => (string) $licensePlate, 'code' => (string) $code]
        );
    }

    /**
     * Retrieves data of a car with a Dutch license plate, including a list of types matched (when available.)
     * This method differs from <carRDWCarDataV2> in that it also returns the CO2 emission.
     *
     * @param string $licensePlate Dutch license plate (kenteken) of the car to retrieve
     *
     * @link https://webview.webservices.nl/documentation/files/service_car-php.html#Car.carRDWCarDataV3
     *
     * @return \stdClass <Car>
     */
    public function carRDWCarData($licensePlate)
    {
        return $this->getAdapter()->call('carRDWCarData', ['license_plate' => (string) $licensePlate]);
    }

    /**
     * Retrieves data of a car with a Dutch license plate.
     * In addition to the information returned by carRDWCarData data on BPM and power is returned.
     *
     * @param string $licensePlate Dutch license plate (kenteken) of the car to retreive
     *
     * @link       https://webview.webservices.nl/documentation/files/service_car-php.html#Car.carRDWCarDataBP
     * @deprecated please use carRDWCarDataBPV2
     *
     * @return \stdClass <Car>
     */
    public function carRDWCarDataBP($licensePlate)
    {
        return $this->getAdapter()->call('carRDWCarDataBP', ['license_plate' => (string) $licensePlate]);
    }

    /**
     * Retrieves data of a car with a Dutch license plate.
     * In addition to the information returned by <carRDWCarData> data on BPM and power is returned.
     *
     * @param string $licensePlate Dutch license plate (kenteken) of the car to retreive
     *
     * @link https://webview.webservices.nl/documentation/files/service_car-php.html#Car.carRDWCarDataBPV2
     *
     * @return \stdClass <CarBPV2>
     */
    public function carRDWCarDataBPV2($licensePlate)
    {
        return $this->getAdapter()->call('carRDWCarDataBPV2', ['license_plate' => (string) $licensePlate]);
    }

    /**
     * Retrieves data of a car with a Dutch license plate and check code ('meldcode').
     * The car data contains the European Approval Mark according to the 2007/46/EG standard. When the code is set it
     * also checks the validity of a license plate and check code ('meldcode') combination.
     *
     * @param string $licensePlate
     * @param string $code
     *
     * @link https://webview.webservices.nl/documentation/files/service_car-php.html#Car.carRDWCarDataExtended
     *
     * @return \stdClass <CarExtended>
     */
    public function carRDWCarDataExtended($licensePlate, $code)
    {
        return $this->getAdapter()->call(
            'carRDWCarDataExtended',
            ['license_plate' => (string) $licensePlate, 'code' => (string) $code]
        );
    }

    /**
     * Retrieves data of a car, including information about extra options.
     *
     * @param string $carId
     *
     * @see Use <carRDWCarDataV3> to find a car_id
     *
     * @return \stdClass <CarOptions>
     */
    public function carRDWCarDataOptions($carId)
    {
        return $this->getAdapter()->call('carRDWCarDataOptions', ['car_id' => (string) $carId]);
    }

    /**
     * Retrieves car data, including the fiscal price, directly from RDW.
     * The fiscal price is the catalogue price of the vehicle, used by the tax department to calculate the tax for the
     * private use of a leased car. Data on the fiscal price, power, environmental impact, status and all information
     * returned by <carRDWCarDataV3>.
     *
     * @param string $licensePlate
     *
     * @link https://webview.webservices.nl/documentation/files/service_car-php.html#Car.carRDWCarDataPrice
     *
     * @return \stdClass <CarRDWCarDataPrice>
     */
    public function carRDWCarDataPrice($licensePlate)
    {
        return $this->getAdapter()->call('carRDWCarDataPrice', ['license_plate' => (string) $licensePlate]);
    }

    /**
     * Retrieves data of a car with a Dutch license plate, including a list of types matched if more information is
     * available. This method differs from <carRDWCarDataV2> in that it also returns the CO2 emission.
     *
     * @param string $licensePlate - Dutch license plate (kenteken) of the car to retrieve
     *
     * @link https://webview.webservices.nl/documentation/files/service_car-php.html#Car.carRDWCarDataV3
     *
     * @return \stdClass <CarDataV3Result>
     */
    public function carRDWCarDataV3($licensePlate)
    {
        return $this->getAdapter()->call('carRDWCarDataV3', ['license_plate' => (string) $licensePlate]);
    }

    /**
     * Retrieve extended information for a car.
     * This function returns more information than <carRDWCarData> or <carRDWCarDataBP>. Please note that when using a
     * test account an older and less complete dataset is used.
     *
     * @param string $licensePlate Dutch license plate (kenteken)
     *
     * @link https://webview.webservices.nl/documentation/files/service_car-php.html#Car.carVWEBasicTypeData
     *
     * @return \stdClass <CarVWEBasicTypeData>
     */
    public function carVWEBasicTypeData($licensePlate)
    {
        return $this->getAdapter()->call('carVWEBasicTypeData', ['license_plate' => (string) $licensePlate]);
    }

    /**
     * Retrieve possible brands for a specific kind of car.
     * Please note that when using a test account an older and less complete data set is used.
     * $kindId:
     *  1 - passenger car, yellow license plate
     *  2 - delivery trucks, company cars, up to 3.5 tons
     *  3 - delivery trucks, company cars, up to 10 tons
     *  4 - off-road four wheel drives
     *  5 - motorcycles
     *  6 - moped
     *  8 - bus.
     *
     * @param int $productionYear Search for brands which produced cars in this year, or one year before or after. If
     *                            0, brands of all years are returned.
     * @param int $kindId         identifier of the kind of car to retrieve the brands for
     * @param int $page           Page to retrieve, pages start counting at 1
     *
     * @return \stdClass <Patterns::{Type}PagedResult> of <CarVWEBrand> entries
     */
    public function carVWEListBrands($productionYear, $kindId, $page)
    {
        return $this->getAdapter()->call(
            'carVWEListBrands',
            ['production_year' => (int) $productionYear, 'kind_id' => (int) $kindId, 'page' => (int) $page]
        );
    }

    /**
     * Retrieve possible models for a specific brand of car.
     *
     * @param int $productionYear Search for models which were produced in this year, or one year before or after. If
     *                            0, models of all years are returned.
     * @param int $kindId         Identifier of the kind of car to retrieve the models for
     * @param int $brandId        brand identifier, as returned by <carVWEListBrands>
     * @param int $page           Page to retrieve, pages start counting at 1
     *
     * @see carVWEList for kindId Options
     *
     * @return \stdClass <Patterns::{Type}PagedResult> of <CarVWEModel> entries
     */
    public function carVWEListModels($productionYear, $kindId, $brandId, $page)
    {
        return $this->getAdapter()->call(
            'carVWEListModels',
            [
                'production_year' => (int) $productionYear,
                'kind_id' => (int) $kindId,
                'brand_id' => (int) $brandId,
                'page' => (int) $page,
            ]
        );
    }

    /**
     * Retrieve possible versions for a specific model of car.
     * Please note that when using a test account an older and less complete dataset is used.
     *
     * @param int $productionYear Search for versions which were produced in this year, or one year before or after.
     *                            If 0, versions of all years are returned.
     * @param int $kindId         Identifier of the kind of car to retrieve the versions for.
     *                            1 - passenger car, yellow license plate
     *                            2 - delivery trucks, company cars, up to 3.5 tons
     *                            3 - delivery trucks, company cars, up to 10 tons
     *                            4 - off-road four wheel drives
     *                            5 - motorcycles
     *                            6 - moped
     *                            8 - bus
     * @param int $brandId        Body style identifier. Optional.
     * @param int $modelId        model identifier, as returned by <carVWEListModels>
     * @param int $fuelTypeId     Fuel type identifier. Optional
     * @param int $bodyStyleId    Body style identifier. Optional.
     *                            02 - 2/4-drs sedan (2/4 deurs sedan)
     *                            03 - 3/5-drs (3/5 deurs hatchback)
     *                            04 - Coupé
     *                            05 - 2-drs (2 deurs cabrio)
     *                            06 - Hardtop
     *                            07 - 3/5-drs (3/5 deurs softtop)
     *                            08 - 2-drs (2 deurs targa)
     *                            09 - 5-drs (5 deurs liftback)
     *                            10 - 3/4/5-drs (combi 3/4/5 deurs)
     *                            14 - afg. pers. auto (afgeleid van personenauto, voertuig met grijs kenteken)
     *                            15 - bedrijfsauto (bestel/bedrijfsauto)
     *                            16 - pers. vervoer (bus, personenvervoer)
     *                            17 - open laadbak (pick-up truck)
     *                            18 - Chassis+Cabine
     *                            19 - Kaal Chassis
     *                            20 - MPV
     *                            21 - SportUtilityVeh (SUV)
     * @param int $doors          Number of doors, If the design is 2/4-drs or 3/5-drs, this parameter can distinguish
     *                            between the two models. Typical values: 2, 3, 4, 5. Optional.
     * @param int $gearId         Type of gearbox. Optional.
     *                            01 - manual transmission
     *                            02 - automatic transmission
     *                            03 - manual, 4 speed
     *                            04 - manual, 5 speed
     *                            05 - manual, 6 speed
     *                            06 - manual, 7 speed
     *                            13 - Semi-automatic
     * @param int $page           Page to retrieve, pages start counting at 1
     *
     * @link https://webview.webservices.nl/documentation/files/service_car-php.html#Car.carVWEListVersions
     *
     * @return \stdClass <Patterns::{Type}PagedResult> of <CarVWEVersion> entries
     */
    public function carVWEListVersions(
        $productionYear,
        $kindId,
        $brandId,
        $modelId,
        $fuelTypeId,
        $bodyStyleId,
        $doors,
        $gearId,
        $page
    ) {
        return $this->getAdapter()->call(
            'carVWEListVersions',
            [
                'production_year' => (int) $productionYear,
                'kind_id' => (int) $kindId,
                'brand_id' => (int) $brandId,
                'model_id' => (int) $modelId,
                'fuel_type_id' => (int) $fuelTypeId,
                'body_style_id' => (int) $bodyStyleId,
                'doors' => (int) $doors,
                'gear_id' => (int) $gearId,
                'page' => (int) $page,
            ]
        );
    }

    /**
     * Check the validity of a license plate and check code ('meldcode') combination.
     * This method differs from <carVWEMeldcodeCheck> in that it also returns whether a car is active.
     *
     * @param string $licensePlate Dutch license plate (kenteken)
     * @param int    $code         code (meldcode), 4 digits
     *
     * @link https://webview.webservices.nl/documentation/files/service_car-php.html#Car.carRDWCarCheckCode
     *
     * @return \stdClass <CarCheckCode>
     */
    public function carVWEMeldcodeCheck($licensePlate, $code)
    {
        return $this->getAdapter()->call(
            'carVWEMeldcodeCheck',
            ['license_plate' => (string) $licensePlate, 'code' => (string) $code]
        );
    }

    /**
     * Retrieve options of a car.
     * The atlCode can be obtained using <carVWEListBrands>, <carVWEListModels> and <carVWEListVersions> consecutively
     * or by using <carVWEBasicTypeData> when it concerns a specific car (requires license plate).
     *
     * @param string $licensePlate The license plate of a car
     * @param int    $atlCode      Code identifying the version of the car
     *
     * @see  carVWEBasicTypeData
     * @link https://webview.webservices.nl/documentation/files/service_car-php.html#Car.carVWEOptions
     *
     * @return \stdClass <CarVWEOptions>
     */
    public function carVWEOptions($licensePlate, $atlCode)
    {
        return $this->getAdapter()->call(
            'carVWEOptions',
            ['license_plate' => (string) $licensePlate, 'atl_code' => (int) $atlCode]
        );
    }

    /**
     * Retrieve photos of a car using it's unique atlCode.
     *
     * @param int $atlCode Code identifying the version of the car. atlCode can be obtained using <carVWEBasicTypeData>,
     *
     * @link https://webview.webservices.nl/documentation/files/service_car-php.html#Car.carVWEPhotos
     *
     * @return \stdClass <CarVWEPhoto>
     */
    public function carVWEPhotos($atlCode)
    {
        return $this->getAdapter()->call('carVWEPhotos', ['atl_code' => (int) $atlCode]);
    }

    /**
     * Retrieve extended information for a specific version of a car.
     * Please note that when using a test account an older and less complete dataset is used.
     *
     * @param string $licensePlate Dutch license plate (kenteken)
     * @param int    $atlCode      Code identifying the version of the car. The ATL code can be obtained using
     *                             <carVWEBasicTypeData>.
     *
     * @link https://webview.webservices.nl/documentation/files/service_car-php.html#Car.carVWEVersionPrice
     *
     * @return \stdClass <CarVWEVersionPrice>
     */
    public function carVWEVersionPrice($licensePlate, $atlCode)
    {
        return $this->getAdapter()->call(
            'carVWEVersionPrice',
            ['license_plate' => (string) $licensePlate, 'atl_code' => (int) $atlCode]
        );
    }

    /**
     * Retrieve extended information for a specific version of a car.
     *
     * @param int $productionYear Get information for the model produced in this year. This affects the <CarVWEPrices>
     *                            in the result
     * @param int $atlCode        Code identifying the version of the car
     *
     * @link https://webview.webservices.nl/documentation/files/service_car-php.html#Car.carVWEVersionYearData
     *
     * @return \stdClass <CarVWEVersionYearData>
     */
    public function carVWEVersionYearData($productionYear, $atlCode)
    {
        return $this->getAdapter()->call(
            'carVWEVersionYearData',
            ['production_year' => $productionYear, 'atl_code' => $atlCode]
        );
    }

    /**
     * Create a testUser.
     *
     * @param string $application
     * @param string $email
     * @param string $companyName
     * @param string $contactName
     * @param string $telephone
     *
     * @return \stdClass <User>
     */
    public function createTestUser($application, $email, $companyName, $contactName, $telephone)
    {
        return $this->getAdapter()->call(
            'createTestUser',
            [
                'application' => $application,
                'email' => $email,
                'companyname' => $companyName,
                'contactname' => $contactName,
                'telephone' => $telephone,
            ]
        );
    }

    /**
     * Retrieve a detailed company report.
     *
     * @param int    $companyId companyID, as returned by <creditsafeSearch>. Due to legal reasons all report requests
     *                          of German companies (DE) must be accompanied with a reason code. To specify a report
     *                          request reason code, append one of the following codes onto the company_id (without
     *                          quotes):
     *                          '|1' -- Credit inquiry
     *                          '|2' -- Business Relationship
     *                          '|3' -- Solvency Check
     *                          '|4' -- Claim
     *                          '|5' -- Contract
     *                          '|6' -- Commercial Credit Insurance
     * @param string $language  ISO 639-1 notation language that the report should be returned in, for example: "EN"
     * @param string $document  Specify to retrieve an extra document with an excerpt of the data. Currently unused.
     *                          Possible values: [empty string] -- Return no extra document.
     *
     * @return \stdClass <CreditsafeCompanyReportFull>
     */
    public function creditsafeGetReportFull($companyId, $language, $document)
    {
        return $this->getAdapter()->call(
            'creditsafeGetReportFull',
            ['company_id' => (int) $companyId, 'language' => (string) $language, 'document' => (string) $document]
        );
    }

    /**
     * Search for a company.
     * The parameters which can be used differ per country.
     *
     * @param string $country            The country to search in, An ISO 3166-1 alpha-2 country code, optional
     * @param string $id                 Search a single company, using the Creditsafe company identifier, optional
     * @param string $registrationNumber Search using a company registration number, optional
     * @param string $status             Search using a company status. See <Country parameters> for allowed values
     *                                   per country, optional
     * @param string $officeType         Search using a company office type. See <Country parameters> for allowed
     *                                   values per country, optional
     * @param string $name               Search using a company name, optional
     * @param string $nameMatchType      How to match the text in the *name* parameter, the default match and
     *                                   possibles types are given in <Country parameters> for each country, optional
     * @param string $address            Search using a company's complete address, optional
     * @param string $addressMatchType   How to match the text in the *address* parameter, the default match type
     *                                   and possibles types are given in <Country parameters> for each country,
     *                                   optional
     * @param string $street             Company's address street, optional
     * @param string $houseNumber        Company's address house number, optional
     * @param string $city               Company's address city, optional
     * @param string $postalCode         Company's address postal code, optional
     * @param string $province           Company's address province, optional
     * @param string $phoneNumber        Company's phone number, optional
     * @param int    $page               Page of search results to retrieve
     *
     * @return \stdClass <Patterns::{Type}PagedResult> of <CreditsafeCompany> entries
     */
    public function creditsafeSearch(
        $country,
        $id,
        $registrationNumber,
        $status,
        $officeType,
        $name,
        $nameMatchType,
        $address,
        $addressMatchType,
        $street,
        $houseNumber,
        $city,
        $postalCode,
        $province,
        $phoneNumber,
        $page
    ) {
        return $this->getAdapter()->call(
            'creditsafeSearch',
            [
                'country' => $country,
                'id' => $id,
                'registration_number' => $registrationNumber,
                'status' => $status,
                'office_type' => $officeType,
                'name' => $name,
                'name_match_type' => $nameMatchType,
                'address' => $address,
                'address_match_type' => $addressMatchType,
                'street' => $street,
                'house_number' => $houseNumber,
                'city' => $city,
                'postal_code' => $postalCode,
                'province' => $province,
                'phone_number' => $phoneNumber,
                'page' => $page,
            ]
        );
    }

    /**
     * Perform a Dun & Bradstreet Business Verification on a business.
     * Returns information on location, situation, size and financial status on the business. The field companyIdType
     * indicates the type of this field.
     * Several types of company identifiers are used within this service. Methods that retrieve company data all types
     * of identifiers: DUNS number, D&B key, and regional business number. All company references returned by search
     * methods are identified using D&B keys.
     *
     * @param string $companyId     identifier for the business
     * @param string $companyIdType Type of company identifier
     *
     * @link   https://webview.webservices.nl/documentation/files/service_dnb-php.html#DunBradstreet.dnbBusinessVerification
     *
     * @return \stdClass <DNBBusinessVerification>
     */
    public function dnbBusinessVerification($companyId, $companyIdType)
    {
        return $this->getAdapter()->call(
            'dnbBusinessVerification',
            ['company_id' => $companyId, 'company_id_type' => $companyIdType]
        );
    }

    /**
     * Retrieve extensive management Dun & Bradstreet Business information
     * See <Dun & Bradstreet::Company identifiers>.
     *
     * @param string $companyId     identifier for the business
     * @param string $companyIdType Type of company identifier
     *
     * @link   https://webview.webservices.nl/documentation/files/service_dnb-php.html#DunBradstreet.dnbEnterpriseManagement
     *
     * @return \stdClass <DNBBusinessVerification>
     */
    public function dnbEnterpriseManagement($companyId, $companyIdType)
    {
        return $this->getAdapter()->call(
            'dnbEnterpriseManagement',
            ['company_id' => $companyId, 'company_id_type' => $companyIdType]
        );
    }

    /**
     * Retrieve a Dun & Bradstreet Business Verification for a business.
     * See <Dun & Bradstreet::Company identifiers>.
     *
     * @param string $companyId     Identifier for the business
     * @param string $companyIdType Type of company identifier
     *
     * @link https://webview.webservices.nl/documentation/files/service_dnb-php.html#DunBradstreet.dnbGetReference
     *
     * @return \stdClass <DNBBusinessVerification>
     */
    public function dnbGetReference($companyId, $companyIdType)
    {
        return $this->getAdapter()->call(
            'dnbGetReference',
            ['company_id' => $companyId, 'company_id_type' => $companyIdType]
        );
    }

    /**
     * Do a Dun & Bradstreet Business Quick Check on a business.
     * See <Dun & Bradstreet::Company identifiers>.
     *
     * @param string $companyId     Identifier for the business
     * @param string $companyIdType Type of company identifier
     *                              Possible values:
     *                              duns    - DUNS number
     *                              dnb_key - D&B business key
     *                              nl|us|.. - 2 character ISO 3166-1 country code. Use this if the companyId is a
     *                              regional business number. For the Netherlands (NL) it can either be an 8-digit
     *                              Chamber of Commerce Number (KvK-nummer), a 12-digit Establishment Number
     *                              (Vestigingsnummer), or a 9-digit RSIN Number
     *
     * @link https://webview.webservices.nl/documentation/files/service_dnb-php.html#DunBradstreet.dnbQuickCheck
     *
     * @return \stdClass <DNBQuickCheck>
     */
    public function dnbQuickCheck($companyId, $companyIdType)
    {
        return $this->getAdapter()->call(
            'dnbQuickCheck',
            ['company_id' => $companyId, 'company_id_type' => $companyIdType]
        );
    }

    /**
     * Search for a business on name and location.
     * This method returns basic information and a DNB business key for each business. Business can be searched on name
     * with optional address parameters. Searching on address can be done using the postcode, or the city and at least
     * one other address field.
     * See <Dun & Bradstreet::Company identifiers>.
     *
     * @param string $name            trade name of the business, required
     * @param string $streetName      street the business is located at, optional
     * @param string $houseNo         house number of the business, optional
     * @param string $houseNoAddition house number addition, optional
     * @param string $postcode        postcode of the business, optional
     * @param string $cityName        city where the business is located, optional
     * @param string $country         The 2 character ISO 3166-1 code for the country where the business is located.
     *                                Required
     * @param int    $page            page to retrieve, pages start counting at 1
     *
     * @deprecated use <dnbSearchReferenceV2> instead
     *
     * @return \stdClass <DNBBusinessVerification>
     */
    public function dnbSearchReference(
        $name,
        $streetName,
        $houseNo,
        $houseNoAddition,
        $postcode,
        $cityName,
        $country,
        $page
    ) {
        return $this->getAdapter()->call(
            'dnbSearchReference',
            [
                'name' => $name,
                'streetname' => $streetName,
                'houseno' => $houseNo,
                'housenoaddition' => $houseNoAddition,
                'postcode' => $postcode,
                'cityname' => $cityName,
                'country' => $country,
                'page' => $page,
            ]
        );
    }

    /**
     * Search for a business on name and location.
     * This method returns basic information and a DNB business key for each business. Business can be searched on name
     * with optional address parameters. Searching on address can be done using the postcode, or the city and at least
     * one other address field.
     *
     * @param string $name            trade name of the business, required
     * @param string $streetName      Street the business is located at, optional
     * @param string $houseNo         House number of the business, optional
     * @param string $houseNoAddition House number addition, optional
     * @param string $postcode        Postcode of the business, optional
     * @param string $cityName        City where the business is located, optional
     * @param string $region          depending on the country, this may be a state, province, or other large
     *                                geographical area
     * @param string $country         For searches in the United States (US) and Canada (CA) this parameter is required.
     *                                State abbreviations, such as NY for New York, must be used
     *                                for the US.
     * @param int    $page
     *
     * @link https://webview.webservices.nl/documentation/files/service_dnb-php.html#DunBradstreet.dnbSearchReferenceV2
     *
     * @return \stdClass <Patterns::{Type}PagedResult> of <DNBBusinessReferenceV2> entries
     */
    public function dnbSearchReferenceV2(
        $name,
        $streetName,
        $houseNo,
        $houseNoAddition,
        $postcode,
        $cityName,
        $region,
        $country,
        $page
    ) {
        return $this->getAdapter()->call(
            'dnbSearchReferenceV2',
            [
                'name' => $name,
                'streetname' => $streetName,
                'houseno' => $houseNo,
                'housenoaddition' => $houseNoAddition,
                'postcode' => $postcode,
                'cityname' => $cityName,
                'region' => $region,
                'country' => $country,
                'page' => $page,
            ]
        );
    }

    /**
     * Retrieve basic WorldBase business information.
     * See <Dun & Bradstreet::Company identifiers>.
     *
     * @param string $companyId     identifier for the business
     * @param string $companyIdType Type of company identifier. see <Dun & Bradstreet::Company identifiers>
     *
     * @link https://webview.webservices.nl/documentation/files/service_dnb-php.html#DunBradstreet.dnbWorldbaseMarketing
     *
     * @return \stdClass <DNBMarketing>
     */
    public function dnbWorldbaseMarketing($companyId, $companyIdType)
    {
        return $this->getAdapter()->call(
            'dnbWorldbaseMarketing',
            ['company_id' => $companyId, 'company_id_type' => $companyIdType]
        );
    }

    /**
     * Retrieve detailed WorldBase business information.
     *
     * @param string $companyId     identifier for the business
     * @param string $companyIdType Type of company identifier. see <Dun & Bradstreet::Company identifiers>
     *
     * @link https://webview.webservices.nl/documentation/files/service_dnb-php.html#DunBradstreet.dnbWorldbaseMarketingPlus
     *
     * @return \stdClass <DNBMarketingPlusResult>
     */
    public function dnbWorldbaseMarketingPlus($companyId, $companyIdType)
    {
        return $this->getAdapter()->call(
            'dnbWorldbaseMarketingPlus',
            ['company_id' => $companyId, 'company_id_type' => $companyIdType]
        );
    }

    /**
     * Detailed WorldBase information, including information on a business' family tree.
     *
     * @param string $companyId     identifier for the business
     * @param string $companyIdType Type of company identifier. see <Dun & Bradstreet::Company identifiers>
     *
     * @link  https://webview.webservices.nl/documentation/files/service_dnb-php.html#DunBradstreet.dnbWorldbaseMarketingPlusLinkage
     *
     * @return \stdClass <DNBMarketingPlusLinkageResult>
     */
    public function dnbWorldbaseMarketingPlusLinkage($companyId, $companyIdType)
    {
        return $this->getAdapter()->call(
            'dnbWorldbaseMarketingPlusLinkage',
            ['company_id' => $companyId, 'company_id_type' => $companyIdType]
        );
    }

    /**
     * Lookup the driving distance in meters between two neighborhood codes for both the fastest and shortest route.
     *
     * @param string $nbCodefrom neighborhood code at start of route
     * @param string $nbCodeto   destination neighborhoodcode
     *
     * @link https://webview.webservices.nl/documentation/files/service_driveinfo-php.html#Driveinfo.driveInfoDistanceLookup
     *
     * @return \stdClass <DriveInfo>
     */
    public function driveInfoDistanceLookup($nbCodefrom, $nbCodeto)
    {
        return $this->getAdapter()->call(
            'driveInfoDistanceLookup',
            ['nbcodefrom' => $nbCodefrom, 'nbcodeto' => $nbCodeto]
        );
    }

    /**
     * Lookup the driving time in minutes between two neighborhood codes for both the fastest and shortest route.
     *
     * @param string $nbCodefrom neighborhood code at start of route
     * @param string $nbCodeto   destination neighborhoodcode
     *
     * @link https://webview.webservices.nl/documentation/files/service_driveinfo-php.html#Driveinfo.driveInfoTimeLookup
     *
     * @return \stdClass <DriveInfo>
     */
    public function driveInfoTimeLookup($nbCodefrom, $nbCodeto)
    {
        return $this->getAdapter()->call(
            'driveInfoTimeLookup',
            ['nbcodefrom' => $nbCodefrom, 'nbcodeto' => $nbCodeto]
        );
    }

    /**
     * Determine if a specific address exists using the unique '1234AA12'
     * postcode + house number format. If returns either the full address in <DutchAddressPostcodeRange> format,
     * or an error if no matching address exists.
     *
     * @param string $address address to validate, in the unique '1234AA12' postcode house number format
     *
     * @return \stdClass <DutchAddressPostcodeRange>
     */
    public function dutchAddressRangePostcodeSearch($address)
    {
        return $this->getAdapter()->call('dutchAddressRangePostcodeSearch', ['address' => $address]);
    }

    /**
     * Retrieve data on a business establishment.
     * When only the dossier number parameter is specified, the main establishment of the business will be returned.
     * Specify the establishment_number in order to retrieve another establishment. You can find dossier and
     * establishment numbers using <dutchBusinessSearchParameters> or <dutchBusinessSearchDossierNumber>. If logging of
     * data is enabled for the user, the requested dossier is logged. This enables the user to receive updates to the
     * dossier in the future. See <Dutch Business update service methods>.
     *
     * @param int      $dossierNumber       The Chamber of Commerce number
     * @param int|null $establishmentNumber The Establishment number
     *
     * @link https://webview.webservices.nl/documentation/files/service_dutchaddress-php.html#Dutch_Address.dutchAddressRangePostcodeSearch
     *
     * @return \stdClass <DutchBusinessDossier>
     */
    public function dutchBusinessGetDossier($dossierNumber, $establishmentNumber = null)
    {
        return $this->getAdapter()->call(
            'dutchBusinessGetDossier',
            ['dossier_number' => $dossierNumber, 'establishment_number' => $establishmentNumber]
        );
    }

    /**
     *  Get a list of logged updates for a specific business dossier.
     *
     * @param string      $dossierNumber   chamber of Commerce number
     * @param string      $periodStartDate Period start date, in Y-m-d format
     * @param string|null $periodEndDate   Period end date, in Y-m-d format. The max period is one year. [optional]
     *
     * @return \stdClass <DutchBusinessDossierHistory>
     */
    public function dutchBusinessGetDossierHistory($dossierNumber, $periodStartDate, $periodEndDate = null)
    {
        return $this->getAdapter()->call(
            'dutchBusinessGetDossierHistory',
            [
                'dossier_number' => $dossierNumber,
                'period_start_date' => $periodStartDate,
                'period_end_date' => $periodEndDate, ]
        );
    }

    /**
     *  Starts a UBO investigation.
     *
     * @param string      $dossierNumber   chamber of Commerce number
     * @param string|null $oldestExtractDate Period start date, in Y-m-d format [optional]
     * @param bool        $useUpdates   Use a real-time extract [optional]
     *
     * @return \stdClass <DutchBusinessUBOInvestigationToken>
     */
    public function dutchBusinessUBOStartInvestigation($dossierNumber, $oldestExtractDate = null, $useUpdates = true)
    {
        return $this->getAdapter()->call('dutchBusinessUBOStartInvestigation', [
            'dossier_number' => $dossierNumber,
            'oldest_extract_date' => $oldestExtractDate,
            'use_updates' => $useUpdates,
        ]);
    }

    /**
     *  Checks the status of an (ongoing) UBO investigation.
     *
     * @param string      $token   An investigation token.
     *
     * @return \stdClass <DutchBusinessUBOInvestigationStatus>
     */
    public function dutchBusinessUBOCheckInvestigation($token)
    {
        return $this->getAdapter()->call('dutchBusinessUBOCheckInvestigation', [
            'token' => $token,
        ]);
    }

    /**
     *  Pick up the results of the UBO investigation.
     *
     * @param string      $token    An investigation token.
     * @param bool        $includeSource    When set the original source is added to the extracts.
     *
     * @return \stdClass <DutchBusinessUBOInvestigationResult>
     */
    public function dutchBusinessUBOPickupInvestigation($token, $includeSource = false)
    {
        return $this->getAdapter()->call('dutchBusinessUBOPickupInvestigation', [
            'token' => $token,
            'include_source' => $includeSource,
        ]);
    }

    /**
     *  Retrieve a list of person entities based on search criteria.
     *
     * @param string      $firstName   First name [optional]
     * @param string      $lastName    Last name (required)
     * @param string      $dateOfBirth    Date of birth (optional, format: Y-m-d)
     * @param int         $page    Pagination starts at 1 (optional, defaults to first page)
     *
     * @return \stdClass <CompliancePersonSearchReference>
     */
    public function complianceSearchPersons($firstName, $lastName, $dateOfBirth, $page = 1)
    {
        return $this->getAdapter()->call('complianceSearchPersons', [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'date_of_birth' => $dateOfBirth,
            'page' => $page,
        ]);
    }

    /**
     *  Search for an overview of Corporate Group Relationships aka ‘concern relaties’ for specified dossier number.
     *  The overview gives a summary of the concern-relations size and depth.
     *
     * @param string $dossierNumber Chamber of Commerce number
     *
     * @return \stdClass <DutchBusinessGetConcernRelationsOverviewResult>
     */
    public function dutchBusinessGetConcernRelationsOverview($dossierNumber)
    {
        return $this->getAdapter()->call('dutchBusinessGetConcernRelationsOverview', ['dossier_number' => $dossierNumber]);
    }

    /**
     *  Search for an overview of Corporate Group Relationships aka ‘concern relaties’ for specified dossier number.
     *  The overview gives a summary of the concern-relations size and depth.
     *
     * @param string $dossierNumber Chamber of Commerce number
     * @param bool   $includeSource  When set the original source is added to the response
     *
     * @return \stdClass <DutchBusinessGetConcernRelationsDetailsResult>
     */
    public function dutchBusinessGetConcernRelationsDetails($dossierNumber, $includeSource)
    {
        return $this->getAdapter()->call('dutchBusinessGetConcernRelationsDetails', ['dossier_number' => $dossierNumber, 'include_source' => $includeSource]);
    }

    /**
     * Get an extract document in PDF, containing the available Chamber of Commerce data for a business.
     * The document is generated using the business' `Online inzage uittreksel`.
     *
     * @param string $dossierNumber Chamber of Commerce number
     * @param bool   $allowCaching  determines whether a cached document may be returned
     *
     * @see <DutchBusinessExtractDocumentData>
     *
     * @return \stdClass <DutchBusinessExtractDocument>
     */
    public function dutchBusinessGetExtractDocument($dossierNumber, $allowCaching)
    {
        return $this->getAdapter()->call(
            'dutchBusinessGetExtractDocument',
            ['dossier_number' => (string) $dossierNumber, 'allow_caching' => (bool) $allowCaching]
        );
    }

    /**
     * Get the data from an extract document containing the available Chamber of Commerce data for a business.
     * The document is generated using the business' `Online inzage uittreksel`.
     *
     * @param string $dossierNumber Chamber of Commerce number
     * @param bool   $allowCaching  Determines whether a cached document may be returned
     *
     * @link       https://webview.webservices.nl/documentation/files/service_dutchbusiness-php.html#Dutch_Business.dutchBusinessGetExtractDocumentData
     * @deprecated please use <DutchBusinessExtractDocumentV2>
     *
     * @return \stdClass <DutchBusinessExtractDocument>
     */
    public function dutchBusinessGetExtractDocumentData($dossierNumber, $allowCaching)
    {
        return $this->getAdapter()->call(
            'dutchBusinessGetExtractDocumentData',
            ['dossier_number' => $dossierNumber, 'allow_caching' => $allowCaching]
        );
    }

    /**
     * Get the extract data and document for a business dossier.
     *
     * @param string $dossierNumber Chamber of Commerce number
     *
     * @link https://webview.webservices.nl/documentation/files/service_dutchbusiness-php.html#Dutch_Business.dutchBusinessGetExtractDocumentDataV2
     *
     * @return \stdClass <DutchBusinessExtractDocumentV2>
     */
    public function dutchBusinessGetExtractDocumentDataV2($dossierNumber)
    {
        return $this->getAdapter()->call('dutchBusinessGetExtractDocumentDataV2', ['dossier_number' => $dossierNumber]);
    }

    /**
     * Get the extract data and document for a business dossier.
     *
     * @param string $dossierNumber Chamber of Commerce number
     * @param bool   $includeSource When set the original source is added to the response
     *
     * @return \stdClass <DutchBusinessExtractDocumentV3>
     *
     * @link https://webview.webservices.nl/documentation/files/service_dutchbusiness-php.html#Dutch_Business.dutchBusinessGetExtractDocumentDataV3
     */
    public function dutchBusinessGetExtractDocumentDataV3($dossierNumber, $includeSource = false)
    {
        return $this->getAdapter()->call(
            'dutchBusinessGetExtractDocumentDataV3',
            ['dossier_number' => $dossierNumber, 'include_source' => $includeSource]
        );
    }

    /**
     * Get a list of historical business-extract references for the given company or organisation.
     * Each business-extract reference in the history contains a summary of the changes relative to the previous
     * business-extract reference in the history. The business-extract history also contains an forecast that indicates
     * whether changes have occured between the latest business-extract document and the current state or the
     * organisation. When changes are detected the most recent document in the history probably does not represent the
     * current state of the organisation. A real-time document can be retrieved using
     * <dutchBusinessGetExtractDocumentData> or <dutchBusinessGetExtractDocument>.
     *
     * @param string $dossierNumber   Chamber of Commerce number
     * @param string $periodStartDate the start date of the period of historic documents
     * @param string $periodEndDate   The end date of the set period, can differ max one year from start date.
     *                                [optional][default:today]
     *
     * @link https://webview.webservices.nl/documentation/files/service_dutchbusiness-php.html#Dutch_Business.dutchBusinessGetExtractHistory
     *
     * @return \stdClass <DutchBusinessExtractHistory>
     */
    public function dutchBusinessGetExtractHistory($dossierNumber, $periodStartDate, $periodEndDate)
    {
        return $this->getAdapter()->call(
            'dutchBusinessGetExtractHistory',
            [
                'dossier_number' => $dossierNumber,
                'period_start_date' => $periodStartDate,
                'period_end_date' => $periodEndDate,
            ]
        );
    }

    /**
     * Get a list of historical business-extract references for the given company or organisation.
     * Collected by Webservices.nl that contain changes compared to their previous retrieved extract.
     *
     * @param string $dossierNumber   chamber of Commerce number
     * @param string $periodStartDate the start date of the period of historic documents
     * @param string $periodEndDate   The end date of the set period. [optional][default:today]
     *
     * @return \stdClass <DutchBusinessExtractHistory>
     */
    public function dutchBusinessGetExtractHistoryChanged($dossierNumber, $periodStartDate, $periodEndDate)
    {
        return $this->getAdapter()->call(
            'dutchBusinessGetExtractHistoryChanged',
            [
                'dossier_number' => $dossierNumber,
                'period_start_date' => $periodStartDate,
                'period_end_date' => $periodEndDate,
            ]
        );
    }

    /**
     * Retrieve a historical business-extract using a business-extract identifier.
     * Business-extract identifiers can be found using <dutchBusinessGetExtractHistory>.
     *
     * @param string $extractId Business-extract identifier
     *
     * @link https://webview.webservices.nl/documentation/files/service_dutchbusiness-php.html#Dutch_Business.dutchBusinessGetExtractHistoryChanged
     *
     * @return \stdClass <DutchBusinessExtractDocumentData>
     */
    public function dutchBusinessGetExtractHistoryDocumentData($extractId)
    {
        return $this->getAdapter()->call('dutchBusinessGetExtractHistoryDocumentData', ['extract_id' => $extractId]);
    }

    /**
     * Retrieve a historical business-extract using a business-extract identifier.
     * Business-extract identifiers can be found using <dutchBusinessGetExtractHistory>.
     *
     * @param string $extractId Business-extract identifier
     *
     * @link https://webview.webservices.nl/documentation/files/service_dutchbusiness-php.html#Dutch_Business.dutchBusinessGetExtractHistoryDocumentDataV2
     *
     * @return \stdClass <DutchBusinessExtractDocumentDataV2>
     */
    public function dutchBusinessGetExtractHistoryDocumentDataV2($extractId)
    {
        return $this->getAdapter()->call('dutchBusinessGetExtractHistoryDocumentDataV2', ['extract_id' => $extractId]);
    }

    /**
     * Get the legal extract data and document for a business dossier.
     *
     * @param string $dossierNumber Chamber of Commerce number
     *
     * @link https://webview.webservices.nl/documentation/files/service_dutchbusiness-php.html#Dutch_Business.dutchBusinessGetLegalExtractDocumentDataV2
     *
     * @return \stdClass <DutchBusinessExtractDocumentDataV2>
     */
    public function dutchBusinessGetLegalExtractDocumentDataV2($dossierNumber)
    {
        return $this->getAdapter()->call(
            'dutchBusinessGetLegalExtractDocumentDataV2',
            ['dossier_number' => $dossierNumber]
        );
    }

    /**
     * Get the business positions/functionaries for a business.
     *
     * @param string $dossierNumber The Chamber of Commerce number
     *
     * @return \stdClass <DutchBusinessPositions> entry
     */
    public function dutchBusinessGetPositions($dossierNumber)
    {
        return $this->getAdapter()->call('dutchBusinessGetPositions', ['dossier_number' => $dossierNumber]);
    }

    /**
     * Look up a SBI ('Standaard Bedrijfs Indeling 2008') code.
     * Returns the section and its description and all levels of SBI codes and their description, according to the
     * 17-04-2014 version.
     *
     * @param string $sbiCode  a number between 2 and 6 characters
     * @param string $language the language of the resulted sbi code descriptions nl (default) || en (English)
     *
     * @return \stdClass <DutchBusinessSBICodeInfo>
     *
     * @link https://webview.webservices.nl/documentation/files/service_dutchbusiness-php.html#Dutch_Business.dutchBusinessGetSBIDescription
     */
    public function dutchBusinessGetSBIDescription($sbiCode, $language)
    {
        return $this->getAdapter()->call(
            'dutchBusinessGetSBIDescription',
            ['sbi_code' => $sbiCode, 'language' => $language]
        );
    }

    /**
     * @param string $dossierNumber The Chamber of Commerce number
     *
     * @return \stdClass <DutchBusinessVatNumber>
     */
    public function dutchBusinessGetVatNumber($dossierNumber)
    {
        return $this->getAdapter()->call('dutchBusinessGetVatNumber', ['dossier_number' => $dossierNumber]);
    }

    /**
     * Find business establishments for a dossier number.
     * Found dossiers are ordered by relevance, ensuring the establishments that match the search parameters best are
     * listed at the top of the result list. When the dossier_number is omitted, the search behaves similar to the
     * <dutchBusinessSearchParametersV2> method.
     *
     * @param string $dossierNumber       Dossier number for the business
     * @param string $tradeName           Name under which the organisation engages in commercial activity
     * @param string $city                City
     * @param string $street              Street
     * @param string $postcode            postalCode
     * @param int    $houseNumber         house number
     * @param string $houseNumberAddition optional addition
     * @param string $telephoneNumber     telephone number
     * @param string $domainName          Domain name or email. When an email address is given, the domain part of that
     *                                    address is used
     * @param bool   $strictSearch
     * @param int    $page
     *
     * @link https://webview.webservices.nl/documentation/files/service_dutchbusiness-php.html#Dutch_Business.dutchBusinessSearch
     *
     * @return \stdClass <Patterns::{Type}PagedResult> of <DutchBusinessEstablishmentReference>
     */
    public function dutchBusinessSearch(
        $dossierNumber,
        $tradeName,
        $city,
        $street,
        $postcode,
        $houseNumber,
        $houseNumberAddition,
        $telephoneNumber,
        $domainName,
        $strictSearch,
        $page
    ) {
        return $this->getAdapter()->call(
            'dutchBusinessSearch',
            [
                'dossier_number' => $dossierNumber,
                'trade_name' => $tradeName,
                'city' => $city,
                'street' => $street,
                'postcode' => $postcode,
                'house_number' => $houseNumber,
                'house_number_addition' => $houseNumberAddition,
                'telephone_number' => $telephoneNumber,
                'domain_name' => $domainName,
                'strict_search' => $strictSearch,
                'page' => $page,
            ]
        );
    }

    /**
     * Search for business establishments using a known identifier.
     * Any combination of parameters may be specified. Only businesses matching all parameters will be returned.
     *
     * @param string $dossierNumber       The Chamber of Commerce number
     * @param string $establishmentNumber The Establishment number
     * @param string $rsinNumber          The RSIN (`Rechtspersonen Samenwerkingsverbanden Informatie Nummer`) number
     * @param int    $page                Page to retrieve, pages start counting at 1
     *
     * @return \stdClass <Patterns::{Type}PagedResult> of <DutchBusinessReference>
     */
    public function dutchBusinessSearchDossierNumber($dossierNumber, $establishmentNumber, $rsinNumber, $page)
    {
        return $this->getAdapter()->call(
            'dutchBusinessSearchDossierNumber',
            [
                'dossier_number' => $dossierNumber,
                'establishment_number' => $establishmentNumber,
                'rsin_number' => $rsinNumber,
                'page' => $page,
            ]
        );
    }

    /**
     * Search for business establishments using a known identifier.
     * Any combination of parameters may be specified. Only businesses matching all parameters will be returned.
     *
     * @param string $dossierNumber       The Chamber of Commerce number
     * @param string $establishmentNumber The Establishment number
     * @param string $rsinNumber          The RSIN (`Rechtspersonen Samenwerkingsverbanden Informatie Nummer`) number
     * @param int    $page                Page to retrieve, pages start counting at 1
     *
     * @link https://webview.webservices.nl/documentation/files/service_dutchbusiness-php.html#Dutch_Business.dutchBusinessSearchEstablishments
     *
     * @return \stdClass <Patterns::{Type}PagedResult> of <DutchBusinessEstablishmentReference>
     */
    public function dutchBusinessSearchEstablishments($dossierNumber, $establishmentNumber, $rsinNumber, $page)
    {
        return $this->getAdapter()->call(
            'dutchBusinessSearchEstablishments',
            [
                'dossier_number' => $dossierNumber,
                'establishment_number' => $establishmentNumber,
                'rsin_number' => $rsinNumber,
                'page' => $page,
            ]
        );
    }

    /**
     * @param string $tradeName           Name under which the organisation engages in commercial activity
     * @param string $city                City
     * @param string $street              Street
     * @param string $postcode            PostalCode
     * @param int    $houseNumber         House number
     * @param string $houseNumberAddition House number addition
     * @param string $telephoneNumber     Telephone number
     * @param bool   $domainName          Domain name or email address, when an email address is given the domain part
     *                                    of that address is used
     * @param string $strictSearch
     * @param int    $page                Page to retrieve, pages start counting at 1
     *
     * @deprecated see dutchBusinessSearchParametersV2 this version working
     *
     * @return \stdClass
     */
    public function dutchBusinessSearchParameters(
        $tradeName,
        $city,
        $street,
        $postcode,
        $houseNumber,
        $houseNumberAddition,
        $telephoneNumber,
        $domainName,
        $strictSearch,
        $page
    ) {
        return $this->getAdapter()->call(
            'dutchBusinessSearchParameters',
            [
                'trade_name' => $tradeName,
                'city' => $city,
                'street' => $street,
                'postcode' => $postcode,
                'house_number' => $houseNumber,
                'house_number_addition' => $houseNumberAddition,
                'telephone_number' => $telephoneNumber,
                'domain_name' => $domainName,
                'strict_search' => $strictSearch,
                'page' => $page,
            ]
        );
    }

    /**
     * Find business establishments using a variety of parameters.
     * Found dossiers are ordered by relevance, ensuring the dossiers that match the search parameters best are listed
     * at the top of the result list. This method differs from <dutchBusinessSearchParameters> by returning an
     * indication called "match_type" that defines what type of business name was matched upon
     * (see <Tradename match types>).
     * Using the search parameters:
     * - tradeName will be used to search all business names for the dossiers, which include the trade name, legal name
     *   and alternative trade names.
     * - address matched against both the correspondence and establishment addresses of the business.
     * - postbox addresses can be found by specifying 'Postbus' as street, and specifying the postbus number in
     *   the houseNumber parameter.
     *
     * @param string $tradeName
     * @param string $city
     * @param string $street
     * @param string $postcode
     * @param int    $houseNumber
     * @param string $houseNumberAddition
     * @param string $telephoneNumber
     * @param string $domainName
     * @param bool   $strictSearch
     * @param int    $page
     *
     * @return \stdClass <Patterns::{Type}PagedResult> of <DutchBusinessReferenceV2>
     */
    public function dutchBusinessSearchParametersV2(
        $tradeName,
        $city,
        $street,
        $postcode,
        $houseNumber,
        $houseNumberAddition,
        $telephoneNumber,
        $domainName,
        $strictSearch,
        $page
    ) {
        return $this->getAdapter()->call(
            'dutchBusinessSearchParametersV2',
            [
                'trade_name' => $tradeName,
                'city' => $city,
                'street' => $street,
                'postcode' => $postcode,
                'house_number' => $houseNumber,
                'house_number_addition' => $houseNumberAddition,
                'telephone_number' => $telephoneNumber,
                'domain_name' => $domainName,
                'strict_search' => $strictSearch,
                'page' => $page,
            ]
        );
    }

    /**
     * Find business establishments based on postcode and house number.
     * This method can return more matches than <dutchBusinessSearchParameters>.
     *
     * @param string $postcode
     * @param string $houseNumber         House number
     * @param string $houseNumberAddition House number addition
     * @param int    $page                Page to retrieve, pages start counting at 1
     *
     * @return \stdClass <Patterns::{Type}PagedResult> of <DutchBusinessReference>
     */
    public function dutchBusinessSearchPostcode($postcode, $houseNumber, $houseNumberAddition, $page)
    {
        return $this->getAdapter()->call(
            'dutchBusinessSearchPostcode',
            [
                'postcode' => $postcode,
                'house_number' => $houseNumber,
                'house_number_addition' => $houseNumberAddition,
                'page' => $page,
            ]
        );
    }

    /**
     * Search for businesses matching all of the given criteria.
     * Either of these criteria can be left empty or 0. In that case, the criterium is not used. At least one of the
     * criteria parameters must be supplied. At most 100 items may be supplied for the array parameters.
     *
     * @param array  $city               Array of cities. Businesses match if they are located in either of these
     *                                   cities, thus if the establishment address is in one of these cities.
     * @param array  $postcode           Array of postcodes or parts of postcodes. Bussinesses match if they are
     *                                   located in either of these postcodes, or their postcode start with any of the
     *                                   given partial postcodes. Thus, if the establishment address matches with one
     *                                   of the given postcodes. For example, the partial postcode "10" matches most of
     *                                   Amsterdam. Note that it would make little sense to supply both city and
     *                                   postcode.
     * @param array  $sbi                Array of SBI codes or partial SBI codes. Businesses match if they have either
     *                                   of the given SBI codes, or their SBI code starts with the partial SBI code.
     * @param bool   $primarySbiOnly     Match primary SBI only. A business may have up to three SBI codes assigned. If
     *                                   primary_sbi_only is true, businesses only match if their main SBI code matches
     *                                   with one of the codes in the 'sbi' field. If primary_sbi_only is false,
     *                                   businesses are matched if either of the three SBI codes match the 'sbi' field.
     * @param array  $legalForm          Array of integer legal form codes. Bussiness match if they have either of
     *                                   these legalforms. A list of legal form codes can be found in the documentation
     *                                   of <DutchBusinessDossier>.
     * @param int    $employeesMin       Minimum number of employees working at the business
     * @param int    $employeesMax       Maximum number of employees working at the business
     * @param string $economicallyActive Indicates whether the businesses should be economically active
     * @param string $financialStatus    indicates the financial status of the businesses
     * @param string $changedSince       Date in yyyy-mm-dd format. Businesses match if the information about them
     *                                   changed on or after this date.
     * @param string $newSince           Date in yyyy-mm-dd format. Only businesses which were added on or after this
     *                                   date are returned. Note that this does not mean that the company was founded
     *                                   after this date. Companies may be founded and only later be added to the
     *                                   DutchBusiness database.
     * @param int    $page               Page to retrieve, pages start counting at 1
     *
     * @link https://webview.webservices.nl/documentation/files/service_dutchbusiness-php.html#Dutch_Business.dutchBusinessSearchSelection
     *
     * @return \stdClass <Patterns::{Type}PagedResult> of <DutchBusinessReference>
     */
    public function dutchBusinessSearchSelection(
        $city,
        $postcode,
        $sbi,
        $primarySbiOnly,
        $legalForm,
        $employeesMin,
        $employeesMax,
        $economicallyActive,
        $financialStatus,
        $changedSince,
        $newSince,
        $page
    ) {
        return $this->getAdapter()->call(
            'dutchBusinessSearchSelection',
            [
                'city' => $city,
                'postcode' => $postcode,
                'sbi' => $sbi,
                'primary_sbi_only' => $primarySbiOnly,
                'legal_form' => $legalForm,
                'employees_min' => $employeesMin,
                'employees_max' => $employeesMax,
                'economically_active' => $economicallyActive,
                'financial_status' => $financialStatus,
                'changed_since' => $changedSince,
                'new_since' => $newSince,
                'page' => $page,
            ]
        );
    }

    /**
     * Add a dossier to the list of dossiers for which the user wants to receive updates.
     * (the user whose credentials are used to make the call) After adding the dossier any future updates to the
     * dossier
     * can be retrieved using <dutchBusinessUpdateGetDossiers>. Before adding the dossier, call
     * <dutchBusinessUpdateCheckDossier> to make sure you have the latest dossier version.
     *  You do not need to call this method if you have retrieved a dossier using <dutchBusinessGetDossier>, in which
     *  case it has been added automatically.
     *
     * @param string $dossierNumber       Chamber of Commerce number
     * @param string $establishmentNumber Establishment number
     *
     * @link https://webview.webservices.nl/documentation/files/service_dutchbusiness-php.html#Dutch_Business.dutchBusinessUpdateAddDossier
     *
     * @return \stdClass
     */
    public function dutchBusinessUpdateAddDossier($dossierNumber, $establishmentNumber)
    {
        return $this->getAdapter()->call(
            'dutchBusinessUpdateAddDossier',
            ['dossier_number' => $dossierNumber, 'establishment_number' => $establishmentNumber]
        );
    }

    /**
     * Retrieve information on the last change to a business establishment.
     * This method can be used to check for updates on specific dossiers, regardless of whether requested dossiers are
     * logged for the user. A <DutchBusinessUpdateReference> is returned for the most recent update, if any. The
     * <DutchBusinessUpdateReference> contains the date of the latest update to the dossier, as well as the types of
     * updates performed on that date. A fault message is returned if there have never been updates to the dossier. The
     * same fault is returned if the dossier does not exist (or never existed).
     *
     * @param string $dossierNumber       Chamber of Commerce number
     * @param string $establishmentNumber Establishment number
     * @param array  $updateTypes         The types of updates to consider. See <Update types> for a list of types.
     *                                    If the type 'Test' is specified, a <DutchBusinessUpdateReference> is returned
     *                                    with DateLastUpdate set to today, its Update types will contain all the types
     *                                    specified in the request.
     *
     * @link https://webview.webservices.nl/documentation/files/service_dutchbusiness-php.html#Dutch_Business.dutchBusinessUpdateCheckDossier
     *
     * @return \stdClass <DutchBusinessUpdateReference>
     */
    public function dutchBusinessUpdateCheckDossier($dossierNumber, $establishmentNumber, $updateTypes)
    {
        return $this->getAdapter()->call(
            'dutchBusinessUpdateCheckDossier',
            [
                'dossier_number' => $dossierNumber,
                'establishment_number' => $establishmentNumber,
                'update_types' => $updateTypes,
            ]
        );
    }

    /**
     * Retrieve dossier numbers for all dossiers changed since the given date.
     * This method returns a <Patterns::{Type}PagedResult> of <DutchBusinessUpdateReference> entries, for all dossiers
     * which were updated since the given changed_since date, and where the update was one of the given
     * update_types. This method can be called periodically to obtain a list of recently updated dossiers. This
     * list can then be checked against the list of locally stored dossiers, to determine which dossiers that
     * the user has stored are changed and may be updated.
     *
     * @param string $changedSince Date in YYYY-MM-DD format. All dossiers changed on or after this date are returned.
     *                             This date may not be more than 40 days ago.
     * @param array  $updateTypes  The types of updates to consider. See <Update types> for a list of types. This
     *                             method supports the update type 'New' to retrieve dossiers which have been
     *                             registered
     *                             with the DutchBusiness since the changed_since date.
     * @param int    $page         Page to retrieve, pages start counting at 1
     *
     * @link https://webview.webservices.nl/documentation/files/service_dutchbusiness-php.html#Dutch_Business.dutchBusinessUpdateGetChangedDossiers
     *
     * @return \stdClass <Patterns::{Type}PagedResult> of <DutchBusinessUpdateReference>
     */
    public function dutchBusinessUpdateGetChangedDossiers($changedSince, $updateTypes, $page)
    {
        return $this->getAdapter()->call(
            'dutchBusinessUpdateGetChangedDossiers',
            ['changed_since' => $changedSince, 'update_types' => $updateTypes, 'page' => $page]
        );
    }

    /**
     * Returns a list of all dossiers that have been updated since they were last retrieved by the user.
     * (the user whose credentials are used to make the call). If a dossier is returned that is no longer of interest
     * or has the update type 'Removed', calling <dutchBusinessUpdateRemoveDossier> prevents it from occurring in this
     * method's output. If a dossier from the output list is retrieved using <dutchBusinessGetDossier>, a second call
     * to <dutchBusinessUpdateGetDossiers> will not contain the dossier anymore. Every <DutchBusinessUpdateReference>
     * describes a dossier, when it was last updated and what types of updates have occurred since the dossier was last
     * retrieved by the user.
     *
     * @param array $updateTypes A list specifying the types of updates that should be returned. See <Update types> for
     *                           a list of types. If the type 'Test' is specified, an example
     *                           <DutchBusinessUpdateReference> is returned with DateLastUpdate set to today, it's
     *                           update types will contain all the types specified in the request.
     * @param int   $page        The page of results
     *
     * @link https://webview.webservices.nl/documentation/files/service_dutchbusiness-php.html#Dutch_Business.dutchBusinessUpdateGetDossiers
     *
     * @return \stdClass <Patterns::{Type}PagedResult> of <DutchBusinessUpdateReference> entries
     */
    public function dutchBusinessUpdateGetDossiers($updateTypes, $page)
    {
        return $this->getAdapter()->call(
            'dutchBusinessUpdateGetDossiers',
            ['update_types' => $updateTypes, 'page' => $page]
        );
    }

    /**
     * Remove a dossier from the list of dossiers for which the user.
     * (the user whose credentials are used to make the call) wants to receive updates.
     *
     * @param string $dossierNumber       Chamber of Commerce number
     * @param string $establishmentNumber Establishment number
     *
     * @return \stdClass
     */
    public function dutchBusinessUpdateRemoveDossier($dossierNumber, $establishmentNumber)
    {
        return $this->getAdapter()->call(
            'dutchBusinessUpdateRemoveDossier',
            ['dossier_number' => $dossierNumber, 'establishment_number' => $establishmentNumber]
        );
    }

    /**
     * Retrieve information about the current market value of a vehicle.
     *
     * @param string $licensePlate The dutch license plate
     *
     * @link https://webview.webservices.nl/documentation/files/service_dutchvehicle-php.html#Dutch_Vehicle.dutchVehicleGetMarketValue
     *
     * @return \stdClass <DutchVehicleMarketValue>
     */
    public function dutchVehicleGetMarketValue($licensePlate)
    {
        return $this->getAdapter()->call('dutchVehicleGetMarketValue', ['license_plate' => $licensePlate]);
    }

    /**
     * Retrieve information about the ownership of a vehicle.
     *
     * @param string $licensePlate The dutch license plate
     *
     * @link https://webview.webservices.nl/documentation/files/service_dutchvehicle-php.html#Dutch_Vehicle.dutchVehicleGetOwnerHistory
     *
     * @return \stdClass <DutchVehicleOwnerHistory>
     */
    public function dutchVehicleGetOwnerHistory($licensePlate)
    {
        return $this->getAdapter()->call('dutchVehicleGetOwnerHistory', ['license_plate' => $licensePlate]);
    }

    /**
     * Retrieve information about a vehicle purchase/catalog price.
     * This method returns (recalculated) purchase and vehicle reference information, useful to establish the insurance
     * amount.
     *
     * @param string $licensePlate The dutch license plate
     *
     * @link https://webview.webservices.nl/documentation/files/service_dutchvehicle-php.html#Dutch_Vehicle.dutchVehicleGetPurchaseReference
     *
     * @return \stdClass
     */
    public function dutchVehicleGetPurchaseReference($licensePlate)
    {
        return $this->getAdapter()->call('dutchVehicleGetPurchaseReference', ['license_plate' => $licensePlate]);
    }

    /**
     * @param string $licensePlate The dutch license plate
     *
     * @return \stdClass
     */
    public function dutchVehicleGetVehicle($licensePlate)
    {
        return $this->getAdapter()->call('dutchVehicleGetVehicle', ['license_plate' => $licensePlate]);
    }

    /**
     * Provides a credit score for a person identified by a set of parameters.
     *
     * @param string $lastName      The last name of the person
     * @param string $initials      The initials
     * @param string $surnamePrefix The surname prefix, like 'van' or 'de', optional
     * @param string $gender        Gender of the person. `M` or `F`
     * @param string $birthDate     Birth date in the format yyyy-mm-dd
     * @param string $street        Street part of the address
     * @param string $houseNumber   House number, optionally including a house number addition
     * @param string $postcode      Dutch postcode in the format 1234AB
     * @param string $phoneNumber   Home phone number, only numeric characters (e.g. 0201234567), may be empty.
     *
     * @link https://webview.webservices.nl/documentation/files/service_edr-php.html#EDR.edrGetScore
     *
     * @return \stdClass <EDRScore>
     */
    public function edrGetScore(
        $lastName,
        $initials,
        $surnamePrefix,
        $gender,
        $birthDate,
        $street,
        $houseNumber,
        $postcode,
        $phoneNumber
    ) {
        return $this->getAdapter()->call(
            'edrGetScore',
            [
                'last_name' => $lastName,
                'initials' => $initials,
                'surname_prefix' => $surnamePrefix,
                'gender' => $gender,
                'birth_date' => $birthDate,
                'street' => $street,
                'house_number' => $houseNumber,
                'postcode' => $postcode,
                'phone_number' => $phoneNumber,
            ]
        );
    }

    /**
     * Returns the coordinates of the given address in degrees of latitude/longitude.
     * You may either specify an address using postcode and house number, or using city, street and house number.
     * Either postcode or city is required. When the city and street parameters are specified the city name and street
     * name that were matched are returned in the result. If a house number is specified its location is interpolated
     * using coordinates of the address range it belongs to. Accuracy may vary depending on the actual distribution of
     * addresses in the range. For the most accurate house number coordinates, use
     * <Kadaster::kadasterAddressCoordinates>.
     *
     * @param string $postcode Address postcode
     * @param string $city     Address city
     * @param string $street   Address street
     * @param int    $houseNo  Address house number
     *
     * @link https://webview.webservices.nl/documentation/files/service_geolocation-php.html#Geolocation.geoLocationAddressCoordinatesLatLon
     *
     * @return \stdClass <LatLonCoordinatesMatch>
     */
    public function geoLocationAddressCoordinatesLatLon($postcode, $city, $street, $houseNo)
    {
        return $this->getAdapter()->call(
            'geoLocationAddressCoordinatesLatLon',
            [
                'postcode' => $postcode,
                'city' => $city,
                'street' => $street,
                'houseno' => $houseNo,
            ]
        );
    }

    /**
     * Returns the coordinates of the given address in the RD system.
     * You may either specify an address using postcode and house number, or using city, street and house number.
     * Either postcode or city is required. When the city and street parameters are specified the city name and street
     * name that were matched are returned in the result. If a house number is specified its location is interpolated
     * using coordinates of the address range it belongs to. Accuracy may vary depending on the actual distribution of
     * addresses in the range. For the most accurate house number coordinates, use
     * <Kadaster::kadasterAddressCoordinates>.
     *
     * @param string $postcode Address postcode
     * @param string $city     Address city
     * @param string $street   Address street
     * @param int    $houseNo  Address house number
     *
     * @link https://webview.webservices.nl/documentation/files/service_geolocation-php.html#Geolocation.geoLocationAddressCoordinatesRD
     *
     * @return \stdClass <RDCoordinatesMatch>
     */
    public function geoLocationAddressCoordinatesRD($postcode, $city, $street, $houseNo)
    {
        return $this->getAdapter()->call(
            'geoLocationAddressCoordinatesRD',
            ['postcode' => $postcode, 'city' => $city, 'street' => $street, 'houseno' => $houseNo]
        );
    }

    /**
     * Returns a given neighborhood code list sorted in order of increasing distance from a given neighborhood.
     *
     * @param string $nbCodefrom Neighborhoodcode to sort the list on
     * @param array  $nbCodes    Array of neighborhood codes to sort using increasing distance to nbcodefrom
     *
     * @link https://webview.webservices.nl/documentation/files/service_geolocation-php.html#Geolocation.geoLocationDistanceSortedNeighborhoodCodes
     *
     * @return \stdClass <Patterns::{Type}Array> of <SortedPostcode>
     */
    public function geoLocationDistanceSortedNeighborhoodCodes($nbCodefrom, $nbCodes)
    {
        return $this->getAdapter()->call(
            'geoLocationDistanceSortedNeighborhoodCodes',
            ['nbcodefrom' => $nbCodefrom, 'nbcodes' => $nbCodes]
        );
    }

    /**
     * Returns a list of neighborhood codes sorted in order of increasing distance from a given neighborhood.
     * within a given radius (in meters).
     *
     * @link https://webview.webservices.nl/documentation/files/service_geolocation-php.html#Geolocation.geoLocationDistanceSortedNeighborhoodCodesRadius
     *
     * @param string $nbCodefrom Neighborhoodcode at the center of the radius
     * @param int    $radius     Radius from nbcodefrom to search in, in meters
     * @param int    $page       Page to retrieve, pages start counting at 1
     *
     * @return \stdClass <Patterns::{Type}PagedResult> of <SortedPostcode>
     */
    public function geoLocationDistanceSortedNeighborhoodCodesRadius($nbCodefrom, $radius, $page)
    {
        return $this->getAdapter()->call(
            'geoLocationDistanceSortedNeighborhoodCodesRadius',
            ['nbcodefrom' => $nbCodefrom, 'radius' => $radius, 'page' => $page]
        );
    }

    /**
     * Returns a list of postcodes sorted in order of increasing distance from a given postcode, within a given radius.
     * If the radius is larger than 1500 meters, the result will be based on neighborhood codes.
     *
     * @param string $postcodeFrom Postcode at the center of the radius
     * @param int    $radius       Radius from postcodefrom to search in, in meters
     * @param int    $page         Page to retrieve, pages start counting at 1
     *
     * @link https://webview.webservices.nl/documentation/files/service_geolocation-php.html#Geolocation.geoLocationDistanceSortedPostcodesRadius
     *
     * @return \StdCLass <Patterns::{Type}PagedResult> of <SortedPostcode>
     */
    public function geoLocationDistanceSortedPostcodesRadius($postcodeFrom, $radius, $page)
    {
        return $this->getAdapter()->call(
            'geoLocationDistanceSortedPostcodesRadius',
            ['postcodefrom' => $postcodeFrom, 'radius' => $radius, 'page' => $page]
        );
    }

    /**
     * Returns the distance in meters (in a direct line) between two latitude/longitude coordinates. Computed by using
     * the Haversine formula, which is accurate as long as the locations are not antipodal (at the other side of the
     * Earth).
     *
     * @param float $latitudeCoord1  Latitude of the first location
     * @param float $longitudeCoord1 Longitude of the first location
     * @param float $latitudeCoord2  Latitude of the second location
     * @param float $longitudeCoord2 Longitude of the second location
     * @param bool  $inRadians       Indicate if input is in radians (otherwise they are interpreted as degrees)
     *
     * @link https://webview.webservices.nl/documentation/files/service_geolocation-php.html#Geolocation.geoLocationHaversineDistance
     *
     * @return int
     */
    public function geoLocationHaversineDistance(
        $latitudeCoord1,
        $longitudeCoord1,
        $latitudeCoord2,
        $longitudeCoord2,
        $inRadians
    ) {
        return $this->getAdapter()->call(
            'geoLocationHaversineDistance',
            [
                'latitude_coord_1' => $latitudeCoord1,
                'longitude_coord_1' => $longitudeCoord1,
                'latitude_coord_2' => $latitudeCoord2,
                'longitude_coord_2' => $longitudeCoord2,
                'in_radians' => $inRadians, ]
        );
    }

    /**
     * Returns the coordinates of the given address in degrees of latitude/longitude.
     * Most countries are supported by this function. Accuracy of the result may vary between countries. Since the
     * street and city have to contain the complete name and since this method acts with international data, we
     * recommend to use <geoLocationInternationalPostcodeCoordinatesLatLon> if you know the postcode, since working
     * with
     * postcodes is less error prone.
     *
     * @param string $street   Complete street name. Street name may not be abbreviated, but may be empty.
     * @param int    $houseNo  House Number
     * @param string $city     Complete city name. City name may not be abbreviated, but may be empty.
     * @param string $province Province, state, district (depends on country, may not be abbreviated). Ignored if not
     *                         exactly matched.
     * @param string $country  Country of the address. Country can be specified by using ISO3 country codes
     *                         (recommended). Complete country names may not always work
     * @param string $language language used for input and preferred language for the output
     *
     * @link https://webview.webservices.nl/documentation/files/service_geolocation-php.html#Geolocation.geoLocationInternationalAddressCoordinatesLatLon
     *
     * @return \stdClass <LatLonCoordinatesInternationalAddress>
     */
    public function geoLocationInternationalAddressCoordinatesLatLon(
        $street,
        $houseNo,
        $city,
        $province,
        $country,
        $language
    ) {
        return $this->getAdapter()->call(
            'geoLocationInternationalAddressCoordinatesLatLon',
            [
                'street' => $street,
                'houseno' => $houseNo,
                'city' => $city,
                'province' => $province,
                'country' => $country,
                'language' => $language,
            ]
        );
    }

    /**
     * Returns the coordinates of the given address in degrees of latitude/longitude.
     * Most countries are supported by this function. Accuracy of the result may vary between countries.
     *
     * @param string $country    Country of the address. Country can be specified by using ISO3 country codes
     *                           (recommended). Complete country names may not always work.
     * @param string $postalCode Postalcode
     * @param int    $houseNo    House number
     * @param string $street     Complete street name. Street name may not be abbreviated, but may be empty.
     * @param string $city       Complete city name. City name may not be abbreviated, but may be empty.
     * @param string $province   Province, state, district (depends on country, may not be abbreviated). Ignored if not
     *                           exactly matched.
     * @param float  $matchRate  The minimum match level the returned search-results range [0-100]
     * @param string $language   Language used for input and preferred language for the output. Depending on the amount
     *                           of available data and the precision of the result, the output might not match the
     *                           language requested.
     *
     * @link https://webview.webservices.nl/documentation/files/service_geolocation-php.html#Geolocation.geoLocationInternationalAddressCoordinatesLatLonV2
     *
     * @return \stdClass <LatLonCoordinatesInternationalAddress>
     */
    public function geoLocationInternationalAddressCoordinatesLatLonV2(
        $country,
        $postalCode,
        $houseNo,
        $street,
        $city,
        $province,
        $matchRate,
        $language
    ) {
        return $this->getAdapter()->call(
            'geoLocationInternationalAddressCoordinatesLatLonV2',
            [
                'country' => $country,
                'postalcode' => $postalCode,
                'houseno' => $houseNo,
                'street' => $street,
                'city' => $city,
                'province' => $province,
                'matchrate' => $matchRate,
                'language' => $language,
            ]
        );
    }

    /**
     * Returns the address and geoLocation info closest to the specified latitude/longitude coordinate.
     *
     * @param float $latitude  Latitude of the location
     * @param float $longitude Longitude of the location
     *
     * @link https://webview.webservices.nl/documentation/files/service_geolocation-php.html#Geolocation.geoLocationInternationalLatLonToAddress
     *
     * @return \stdClass <GeoLocationInternationalAddress>
     */
    public function geoLocationInternationalLatLonToAddress($latitude, $longitude)
    {
        return $this->getAdapter()->call(
            'geoLocationInternationalLatLonToAddress',
            ['latitude' => $latitude, 'longitude' => $longitude]
        );
    }

    /**
     * Returns the coordinates of the given postcode in degrees of latitude/longitude.
     * Most countries are supported by this function. Accuracy of the result may vary between countries.
     *
     * @param string $postcode Postcode to find the location of (postcode format varies depending on the country
     *                         specified)
     * @param string $country  Country of the address. Country can be specified by using ISO3 country codes
     *                         (recommended). Complete country names may not always work.
     *
     * @link https://webview.webservices.nl/documentation/files/service_geolocation-php.html#Geolocation.geoLocationInternationalPostcodeCoordinatesLatLon
     *
     * @return \stdClass LatLonCoordinates>
     */
    public function geoLocationInternationalPostcodeCoordinatesLatLon($postcode, $country)
    {
        return $this->getAdapter()->call(
            'geoLocationInternationalPostcodeCoordinatesLatLon',
            ['postcode' => $postcode, 'country' => $country]
        );
    }

    /**
     * Return the address and geoLocation info closest to the specified latitude/longitude coordinate in the NL.
     * This method differs from geoLocationLatLonToAddress in that it is more precise: it uses data with
     * house number precision, instead of house number range precision. This means that a specific house number is
     * returned instead of a range, and that the returned address is typically closer to the coordinate than with
     * geoLocationLatLonToAddress. Note that this method may return a different street than geoLocationLatLonToAddress.
     *
     * @param float $latitude  Latitude of the location
     * @param float $longitude Longitude of the location
     *
     * @link https://webview.webservices.nl/documentation/files/service_geolocation-php.html#Geolocation.geoLocationLatLonToAddressV2
     *
     * @return \stdClass <GeoLocationAddressV2>
     */
    public function geoLocationLatLonToAddressV2($latitude, $longitude)
    {
        return $this->getAdapter()->call(
            'geoLocationLatLonToAddressV2',
            ['latitude' => $latitude, 'longitude' => $longitude]
        );
    }

    /**
     * Returns the postcode of the address closest to the specified latitude/longitude coordinate in the Netherlands.
     *
     * @param string $latitude  Latitude of the postcode
     * @param string $longitude Longitude of the postcode
     *
     * @link https://webview.webservices.nl/documentation/files/service_geolocation-php.html#Geolocation.geoLocationLatLonToPostcode
     *
     * @return \stdClass <GeoLocationAddress>
     */
    public function geoLocationLatLonToPostcode($latitude, $longitude)
    {
        return $this->getAdapter()->call(
            'geoLocationLatLonToPostcode',
            ['latitude' => $latitude, 'longitude' => $longitude]
        );
    }

    /**
     * Convert a latitude/longitude coordinate to a RD ('Rijksdriehoeksmeting') coordinate.
     *
     * @param string $latitude  Latitude of the postcode
     * @param string $longitude Longitude of the postcode
     *
     * @link https://webview.webservices.nl/documentation/files/service_geolocation-php.html#Geolocation.geoLocationLatLonToRD
     *
     * @return \stdClass <RDCoordinates>
     */
    public function geoLocationLatLonToRD($latitude, $longitude)
    {
        return $this->getAdapter()->call(
            'geoLocationLatLonToRD',
            ['latitude' => $latitude, 'longitude' => $longitude]
        );
    }

    /**
     * Returns the coordinates in the latitude/longitude system of the neighborhood, given the neighborhood code.
     *
     * @param string $nbCode Neighborhoodcode to find the location of
     *
     * @link https://webview.webservices.nl/documentation/files/service_geolocation-php.html#Geolocation.geoLocationNeighborhoodCoordinatesLatLon
     *
     * @return \stdClass <LatLonCoordinates>
     */
    public function geoLocationNeighborhoodCoordinatesLatLon($nbCode)
    {
        return $this->getAdapter()->call('geoLocationNeighborhoodCoordinatesLatLon', ['nbcode' => (string) $nbCode]);
    }

    /**
     * Returns the coordinates in the RD system of the neighborhood given the neighborhood code.
     *
     * @param string $nbCode Neighborhoodcode to find the location of
     *
     * @link https://webview.webservices.nl/documentation/files/service_geolocation-php.html#Geolocation.geoLocationNeighborhoodCoordinatesRD
     *
     * @return \stdClass <RDCoordinates>
     */
    public function geoLocationNeighborhoodCoordinatesRD($nbCode)
    {
        return $this->getAdapter()->call('geoLocationNeighborhoodCoordinatesRD', ['nbcode' => (string) $nbCode]);
    }

    /**
     * Returns estimated distance in meters (in a direct line) between two neighborhoods, given the neighborhood codes.
     *
     * @param string $nbCodefrom Neighborhood code of the first neighborhood
     * @param string $nbCodeto   Neighborhood code of the second neighborhood
     *
     * @link https://webview.webservices.nl/documentation/files/service_geolocation-php.html#Geolocation.geoLocationNeighborhoodDistance
     *
     * @return int distance in meters
     */
    public function geoLocationNeighborhoodDistance($nbCodefrom, $nbCodeto)
    {
        return $this->getAdapter()->call(
            'geoLocationNeighborhoodDistance',
            ['nbcodefrom' => $nbCodefrom, 'nbcodeto' => $nbCodeto]
        );
    }

    /**
     * Returns the coordinates of the given postcode in degrees of latitude/longitude.
     *
     * @param string $postcode Postcode to find the location of
     *
     * @link https://webview.webservices.nl/documentation/files/service_geolocation-php.html#Geolocation.geoLocationPostcodeCoordinatesLatLon
     *
     * @return \stdClass <LatLonCoordinates>
     */
    public function geoLocationPostcodeCoordinatesLatLon($postcode)
    {
        return $this->getAdapter()->call('geoLocationPostcodeCoordinatesLatLon', ['postcode' => $postcode]);
    }

    /**
     * Returns the coordinates of the given postcode in the RD system.
     *
     * @param string $postcode Postcode to find the location of
     *
     * @link https://webview.webservices.nl/documentation/files/service_geolocation-php.html#Geolocation.geoLocationPostcodeCoordinatesRD
     *
     * @return \stdClass <RDCoordinates>
     */
    public function geoLocationPostcodeCoordinatesRD($postcode)
    {
        return $this->getAdapter()->call('geoLocationPostcodeCoordinatesRD', ['postcode' => $postcode]);
    }

    /**
     * Returns the estimated distance in meters (in a direct line) between two postcodes.
     *
     * @param string $postcodeFrom First postcode
     * @param string $postcodeTo   Second postcode
     *
     * @link https://webview.webservices.nl/documentation/files/service_geolocation-php.html#Geolocation.geoLocationPostcodeDistance
     *
     * @return int distance in meters
     */
    public function geoLocationPostcodeDistance($postcodeFrom, $postcodeTo)
    {
        return $this->getAdapter()->call(
            'geoLocationPostcodeDistance',
            ['postcodefrom' => $postcodeFrom, 'postcodeto' => $postcodeTo]
        );
    }

    /**
     * Returns the address and geoLocation info closest to the specified Rijksdriehoeksmeting X/Y coordinate in NL.
     * This method differs from geoLocationLatLonToAddress in that it is more precise. it uses data with house number
     * precision, instead of house number range precision. This means that a specific house number is returned instead
     * of a range, and that the returned address is typically closer to the coordinate than with
     * geoLocationRDToAddress.
     * Note that this method may return a different street than geoLocationRDToAddress.
     *
     * @param int $posX rd X of the location
     * @param int $posY rd Y of the location
     *
     * @link https://webview.webservices.nl/documentation/files/service_geolocation-php.html#Geolocation.geoLocationRDToAddressV2
     *
     * @return \stdClass <GeoLocationAddressV2>
     */
    public function geoLocationRDToAddressV2($posX, $posY)
    {
        return $this->getAdapter()->call('geoLocationRDToAddressV2', ['x' => $posX, 'y' => $posY]);
    }

    /**
     * Convert a latitude/longitude coordinate to a RD ('Rijksdriehoeksmeting') coordinate.
     *
     * @param int $posX part of the RD coordinate
     * @param int $posY part of the RD coordinate
     *
     * @link  https://webview.webservices.nl/documentation/files/service_geolocation-php.html#Geolocation.geoLocationLatLonToRD
     *
     * @return \stdClass <RDCoordinates>
     */
    public function geoLocationRDToLatLon($posX, $posY)
    {
        return $this->getAdapter()->call('geoLocationRDToLatLon', ['x' => $posX, 'y' => $posY]);
    }

    /**
     * Returns the postcode of the address closest to  Rijksdriehoeksmeting coordinate in the Netherlands
     *
     * @param int $posX part of the RD coordinate
     * @param int $posY part of the RD coordinate
     *
     * @link https://webview.webservices.nl/documentation/files/service_geolocation-php.html#Geolocation.geoLocationRDToPostcode
     *
     * @return \stdClass postcode
     */
    public function geoLocationRDToPostcode($posX, $posY)
    {
        return $this->getAdapter()->call('geoLocationRDToPostcode', ['x' => $posX, 'y' => $posY]);
    }

    /**
     * Retrieve top-parent, parent and sibling companies of a company registered in the Netherlands.
     * If an alarm code is set, no values are returned. Use <graydonCreditGetReport> to retrieve more information about
     * the alarm/calamity.
     *
     * @param int $graydonCompanyId 9 Digit Graydon company identification number
     *
     * @link https://webview.webservices.nl/documentation/files/service_graydoncredit-php.html#Graydon_Credit.graydonCreditCompanyLiaisons
     *
     * @return \stdClass <GraydonCreditReportCompanyLiaisons>
     */
    public function graydonCreditCompanyLiaisons($graydonCompanyId)
    {
        return $this->getAdapter()->call(
            'graydonCreditCompanyLiaisons',
            ['graydon_company_id' => (int) $graydonCompanyId]
        );
    }

    /**
     * Retrieve a Graydon credit report of a company registered in the Netherlands.
     *
     * @param int    $graydonCompanyId 9 Digit Graydon company identification number
     * @param string $document         Specify to retrieve an extra document with an excerpt of the data. Currently
     *                                 unused. Possible values:
     *                                 [empty string] -- Return no extra document.
     *
     * @link https://webview.webservices.nl/documentation/files/service_graydoncredit-php.html#Graydon_Credit.graydonCreditGetReport
     *
     * @return \stdClass <GraydonCreditReport>
     */
    public function graydonCreditGetReport($graydonCompanyId, $document)
    {
        return $this->getAdapter()->call(
            'graydonCreditGetReport',
            ['graydon_company_id' => $graydonCompanyId, 'document' => $document]
        );
    }

    /**
     *  Retrieve information on the management positions in a company registered in the Netherlands.
     * If an alarm code is set, no values are returned. Use <graydonCreditGetReport> to retrieve more information about
     * the alarm/calamity.
     *
     * @param int $graydonCompanyId 9 Digit Graydon company identification number. See <Company Test Identifiers> for a
     *                              list of free test reports.
     *
     * @link https://webview.webservices.nl/documentation/files/service_graydoncredit-php.html#Graydon_Credit.graydonCreditManagement
     *
     * @return \stdClass <GraydonCreditReportManagement>
     */
    public function graydonCreditManagement($graydonCompanyId)
    {
        return $this->getAdapter()->call('graydonCreditManagement', ['graydon_company_id' => $graydonCompanyId]);
    }

    /**
     * Retrieve a Graydon pd ratings and credit flag of a company registered in the Netherlands.
     * If an alarm code is set, no values are returned. Use <graydonCreditGetReport> to retrieve more information about
     * the alarm/calamity.
     *
     * @param int $graydonCompanyId 9 Digit Graydon company identification number
     *
     * @link https://webview.webservices.nl/documentation/files/service_graydoncredit-php.html#Graydon_Credit.graydonCreditQuickscan
     *
     * @return \stdClass <GraydonCreditReportQuickscan>
     */
    public function graydonCreditQuickscan($graydonCompanyId)
    {
        return $this->getAdapter()->call('graydonCreditQuickscan', ['graydon_company_id' => $graydonCompanyId]);
    }

    /**
     * Retrieve various Graydon credit ratings of a company registered in the Netherlands.
     * If an alarm code is set, no values are returned. Use <graydonCreditGetReport> to retrieve more information about
     * the alarm/calamity.
     *
     * @param int $graydonCompanyId 9 Digit Graydon company identification number. See <Company Test Identifiers> for a
     *                              list of free test reports
     *
     * @link https://webview.webservices.nl/documentation/files/service_graydoncredit-php.html#Graydon_Credit.graydonCreditRatings
     *
     * @return \stdClass <GraydonCreditReportRatings>
     */
    public function graydonCreditRatings($graydonCompanyId)
    {
        return $this->getAdapter()->call('graydonCreditRatings', ['graydon_company_id' => $graydonCompanyId]);
    }

    /**
     * Search international Graydon credit report databases for a company using an identifier.
     *
     * @param string $companyId     Company identification
     * @param string $companyIdType Identification type. Supported:
     *                              graydon - 9 digit Graydon company id
     *                              kvk     - 8 digit Dutch Chamber of Commerce (KvK) dossier number, without the sub
     *                              dossier number
     * @param string $countryIso2   Country where the company is registered, country name, ISO 3166 alpha 2 code.
     *                              Supported countries:  nl -- The Netherlands
     *
     * @link https://webview.webservices.nl/documentation/files/service_graydoncredit-php.html#Graydon_Credit.graydonCreditSearchIdentification
     *
     * @return \stdClass <Patterns::{Type}Array> of <GraydonReference>
     */
    public function graydonCreditSearchIdentification($companyId, $companyIdType, $countryIso2)
    {
        return $this->getAdapter()->call(
            'graydonCreditSearchIdentification',
            [
                'company_id' => $companyId,
                'company_id_type' => $companyIdType,
                'country_iso2' => $countryIso2,
            ]
        );
    }

    /**
     * Search the international Graydon credit report database for a company by its name.
     *
     * @param string $companyName Required. Company name, trade name or business name.
     * @param string $residence   Name of the city or region
     * @param string $countryIso2 Country where the company is registered, country name, ISO 3166 alpha 2 code.
     *                            Supported countries: nl -- The Netherlands
     *
     * @link https://webview.webservices.nl/documentation/files/service_graydoncredit-php.html#Graydon_Credit.graydonCreditSearchName
     *
     * @return \stdClass <Patterns::{Type}Array> of <GraydonReference>
     */
    public function graydonCreditSearchName($companyName, $residence, $countryIso2)
    {
        return $this->getAdapter()->call(
            'graydonCreditSearchName',
            ['company_name' => $companyName, 'residence' => $residence, 'country_iso2' => $countryIso2]
        );
    }

    /**
     * Search international Graydon credit report database for a company using its postcode.
     *
     * @param string $postcode    Postcode
     * @param int    $houseNo     House number of the address. Requires input of postcode parameter.
     * @param string $telephoneNo Telephone number
     * @param string $countryIso2 Country where the company is registered, country name, ISO 3166 alpha 2 code.
     *                            Supported countries: nl -- The Netherlands
     *
     * @link https://webview.webservices.nl/documentation/files/service_graydoncredit-php.html#Graydon_Credit.graydonCreditSearchPostcode
     *
     * @return \stdClass <Patterns::{Type}Array> of <GraydonReference>
     */
    public function graydonCreditSearchPostcode($postcode, $houseNo, $telephoneNo, $countryIso2)
    {
        return $this->getAdapter()->call(
            'graydonCreditSearchPostcode',
            [
                'postcode' => $postcode,
                'house_no' => $houseNo,
                'telephone_no' => $telephoneNo,
                'country_iso2' => $countryIso2,
            ]
        );
    }

    /**
     * Retrieve the BTW (VAT) number of a company registered in the Netherlands.
     * If an alarm code is set, no values are returned. Use <graydonCreditGetReport> to retrieve more information about
     * the alarm/calamity.
     *
     * @param int $graydonCompanyId 9 Digit Graydon company identification number.
     *                              See <Company Test Identifiers> for a list of free test reports
     *
     * @link https://webview.webservices.nl/documentation/files/service_graydoncredit-php.html#Graydon_Credit.graydonCreditVatNumber
     *
     * @return \stdClass <GraydonCreditReportVatNumber>
     */
    public function graydonCreditVatNumber($graydonCompanyId)
    {
        return $this->getAdapter()->call('graydonCreditVatNumber', ['graydon_company_id' => $graydonCompanyId]);
    }

    /**
     * Search for publications for a person.
     *
     * @param string $lastName     Persons surname
     * @param string $prefix       Surname prefix (eg. de, van, van der ..)
     * @param string $birthDate    Date of birth (format: yyyy-mm-dd)
     * @param string $postcode     Postcode
     * @param string $houseNumber  House number
     *
     * @return \stdClass <InsolvencyPublicationList>
     *
     * @link https://webview.webservices.nl/documentation/files/service_insolvency-php.html#Insolvency.insolvencySearchPublicationsByPerson
     *
     */
    public function insolvencySearchPublicationsByPerson($lastName, $prefix, $birthDate, $postcode, $houseNumber)
    {
        return $this->getAdapter()->call('insolvencySearchPublicationsByPerson', [
            'last_name' => $lastName,
            'prefix' => $prefix,
            'birth_date' => $birthDate,
            'postcode' => $postcode,
            'house_number' => $houseNumber,
        ]);
    }

    /**
     * Search for publications for a dutch company.
     *
     * @param string $cocNumber  A registration number from the dutch chamber of commerce (dutch: kvk-nummer)
     *
     * @return \stdClass <InsolvencyPublicationList>
     *
     * @link https://webview.webservices.nl/documentation/files/service_insolvency-php.html#Insolvency.insolvencySearchPublicationsByCoCNumber
     *
     */
    public function insolvencySearchPublicationsByCoCNumber($cocNumber)
    {
        return $this->getAdapter()->call('insolvencySearchPublicationsByCoCNumber', [
            'coc_number' => $cocNumber,
        ]);
    }

    /**
     * This method expects an address that is already more or less complete.
     * Checks for the correctness of the specified address, completing it if possible. If suggestions can be generated
     * they will be returned as well. Returns address suggestions related to the address information given. Suggestions
     * are ranked based on a matching percentage. Per field status indications are also returned for every suggestion.
     * Any parameter may be left empty, apart from the country parameter.
     *
     * @param string $organization  Name of the company or organisation at the address
     * @param string $building      Building or sub-building name
     * @param string $street        Street search phrase
     * @param string $houseNr       House number search phrase
     * @param string $poBox         PO box search phrase
     * @param string $locality      District or municipality search phrase
     * @param string $postcode      Postalcode search phrase
     * @param string $province      Province search phrase
     * @param string $country       Country of the address, required. Accepts ISO3 country codes.
     * @param string $language      language in which the results are returned, see <Preferred Language>
     * @param string $countryFormat the format in which the country is returned, see <Country Format>
     *
     * @link https://webview.webservices.nl/documentation/files/service_internationaladdress-php.html#International_Address.internationalAddressSearchInteractive
     *
     * @return \stdClass <InternationalAddressSearchV2Result>
     */
    public function internationalAddressSearchInteractive(
        $organization,
        $building,
        $street,
        $houseNr,
        $poBox,
        $locality,
        $postcode,
        $province,
        $country,
        $language,
        $countryFormat
    ) {
        return $this->getAdapter()->call(
            'internationalAddressSearchInteractive',
            [
                'organization' => $organization,
                'building' => $building,
                'street' => $street,
                'housenr' => $houseNr,
                'pobox' => $poBox,
                'locality' => $locality,
                'postcode' => $postcode,
                'province' => $province,
                'country' => $country,
                'language' => $language,
                'country_format' => $countryFormat,
            ]
        );
    }

    /**
     * This method is suited to handle data entry where only partial address information is provided.
     * Based on the partial information a list of up to 20 addresses is suggested, saving significant key strokes when
     * entering address data. Returns address suggestions related to the address information given. Suggestions are
     * ranked based on a matching percentage. Per field status indications are also returned for every suggestion. Any
     * parameter may be left empty, apart from the country parameter.
     *
     * @param string $organization  Name of the company or organisation at the address
     * @param string $building      Building or sub building name
     * @param string $street        Street search phrase
     * @param string $houseNr       House number search phrase
     * @param string $poBox         PO box search phrase
     * @param string $locality      District or municipality search phrase
     * @param string $postcode      Postalcode search phrase
     * @param string $province      Province search phrase
     * @param string $country       Country of the address, required. Accepts ISO3 country codes.
     * @param string $language      language in which the results are returned, see <Preferred Language>
     * @param string $countryFormat the format in which the country is returned, see <Country Format>
     *
     * @link https://webview.webservices.nl/documentation/files/service_internationaladdress-php.html#International_Address.internationalAddressSearchV2
     *
     * @return \stdClass <InternationalAddressSearchV2Result>
     */
    public function internationalAddressSearchV2(
        $organization,
        $building,
        $street,
        $houseNr,
        $poBox,
        $locality,
        $postcode,
        $province,
        $country,
        $language,
        $countryFormat
    ) {
        return $this->getAdapter()->call(
            'internationalAddressSearchV2',
            [
                'organization' => $organization,
                'building' => $building,
                'street' => $street,
                'housenr' => $houseNr,
                'pobox' => $poBox,
                'locality' => $locality,
                'postcode' => $postcode,
                'province' => $province,
                'country' => $country,
                'language' => $language,
                'country_format' => $countryFormat,
            ]
        );
    }

    /**
     * Returns the coordinates of the given address in both the RD system and the latitude/longitude system.
     * The lat/lon coordinates are derived from the RD coordinates. The address may be specified by giving the
     * postcode, house number & house number addition or by giving the cityname, streetname, house number & house
     * number addition.
     *
     * @param string $postcode        Address postcode
     * @param string $city            Address city
     * @param string $street          Address street
     * @param int    $houseNo         Address house number
     * @param string $houseNoAddition Address house number addition
     *
     * @link https://webview.webservices.nl/documentation/files/service_kadaster-php.html#Kadaster.kadasterAddressCoordinates
     *
     * @return \stdClass <KadasterCoordinates>
     */
    public function kadasterAddressCoordinates($postcode, $city, $street, $houseNo, $houseNoAddition)
    {
        return $this->getAdapter()->call(
            'kadasterAddressCoordinates',
            [
                'postcode' => $postcode,
                'city' => $city,
                'street' => $street,
                'houseno' => $houseNo,
                'housenoaddition' => $houseNoAddition,
            ]
        );
    }

    /**
     * Find a 'bron document', a document which is the basis for an ascription.
     * See <Example documents> for an example document.
     *
     * @param string $aanduidingSoortRegister Identifies the type of register in which the document was registered.
     *                                        Supported values:
     *                                        3 -- Register of mortgage documents (dutch: hypotheekakte)
     *                                        4 -- Register of transport documents (dutch: transportakte)
     * @param string $deel                    identifier for a group of documents within a register of a Kadaster
     * @param string $nummer                  Alphanumeric number used to identify a document. Note that a number does
     *                                        not relate to a single revision of the document, numbers may be reused if
     *                                        a change is issued on time.
     * @param string $reeks                   Identifier for the (former) establishment of the Kadaster where the Stuk
     *                                        was originally registered. This parameter is required for requests where
     *                                        deel is less than 50000, and may be left empty if deel is 50000 or
     *                                        higher. Use <kadasterValueListGetRanges> to get a full list of possible
     *                                        "reeks" values.
     * @param string $format                  Filetype of the result. The result will always be encoded in base 64. If
     *                                        an image format is requested a conversion is performed on our servers,
     *                                        which might cause the response to be delayed for large documents. We
     *                                        recommend using a longer timeout setting for such requests. Supported
     *                                        formats:
     *                                        pdf -- Only a PDF document will be returned.
     *                                        png_16 -- A PDF file, and one PNG image for every page will be returned.
     *                                        Each image is approximately 132 by 187 pixels.
     *                                        png_144 -- A PDF file, and one PNG image for every page will be returned.
     *                                        Each image is approximately 132 by 187 pixels.
     *                                        gif_144 -- A PDF file, and one GIF image for every page will be returned.
     *                                        Each image is approximately 1190 by 1684 pixels.
     *
     * @return \stdClass <KadasterBronDocument>
     */
    public function kadasterBronDocument($aanduidingSoortRegister, $deel, $nummer, $reeks, $format)
    {
        return $this->getAdapter()->call(
            'kadasterBronDocument',
            [
                'aanduiding_soort_register' => $aanduidingSoortRegister,
                'deel' => $deel,
                'nummer' => $nummer,
                'reeks' => $reeks,
                'format' => $format,
            ]
        );
    }

    /**
     * Find a 'Eigendomsbericht' by parcel details.
     * Returns the result in a file of the specified format. If the input matches more than one parcel, a list of the
     * parcels found is returned instead. Sectie, perceelnummer and the code or name of the municipality are required.
     *
     * @param string $gemeenteCode  Municipality code
     * @param string $gemeenteNaam  Municipality name. See <kadasterValueListGetMunicipalities> for possible values.
     * @param string $sectie        Section code
     * @param string $perceelnummer Parcel number
     * @param string $relatieCode   Indicates object relation type, set if object is part of another parcel. If
     *                              relatiecode is specified, volgnummer should be specified as well. Allowed values:
     *                              'A', 'D', or empty.
     * @param string $volgnummer    Object index number, set if object is part of another parcel
     * @param string $format        Filetype of the result. The result will always be encoded in base 64. If an image
     *                              format is requested a conversion is performed on our servers, which might cause the
     *                              response to be delayed for large documents. We recommend using a longer timeout
     *                              setting for such requests. Supported formats: pdf - Only a PDF document will be
     *                              returned. png_16 - A PDF file, and one PNG image for every page will be returned.
     *                              Each image is approximately 132 by 187 pixels. png_144 - A PDF file, and one PNG
     *                              image for every page will be returned. Each image is approximately 1190 by 1684
     *                              pixels. gif_144 - A PDF file, and one GIF image for every page will be returned.
     *                              Each image is approximately 1190 by 1684 pixels.
     *
     * @link https://webview.webservices.nl/documentation/files/service_kadaster-php.html#Kadaster.kadasterEigendomsBerichtDocumentPerceel
     *
     * @return \stdClass <BerichtObjectDocumentResultaat>
     */
    public function kadasterEigendomsBerichtDocumentPerceel(
        $gemeenteCode,
        $gemeenteNaam,
        $sectie,
        $perceelnummer,
        $relatieCode,
        $volgnummer,
        $format
    ) {
        return $this->getAdapter()->call(
            'kadasterEigendomsBerichtDocumentPerceel',
            [
                'gemeentecode' => $gemeenteCode,
                'gemeentenaam' => $gemeenteNaam,
                'sectie' => $sectie,
                'perceelnummer' => $perceelnummer,
                'relatiecode' => $relatieCode,
                'volgnummer' => $volgnummer,
                'format' => $format,
            ]
        );
    }

    /**
     * Find a 'Eigendomsbericht' by postcode and house number.
     * Returns the result in a file of the specified format. If the input matches more than one parcel, a list of the
     * parcels found is returned instead. If an image format is requested a conversion is performed on our servers,
     * which might cause the response to be delayed for large documents. Please use a higher timeout setting.
     *
     * @param string $postcode             Address postcode
     * @param int    $huisNummer           Address house number
     * @param string $huisNummerToevoeging Address house number addition
     * @param string $format               File type of the result. The result will always be encoded in base 64.
     *                                     Supported formats:
     *                                     pdf    - Only a PDF document will be returned.
     *                                     png_16 - A PDF file, and one PNG image for every page will be returned.
     *                                     Each image is approximately 132 by 187 pixels.
     *                                     png_144 - A PDF file, and one PNG image for every page will be returned.
     *                                     Each image is approximately 1190 by 1684 pixels.
     *                                     gif_144 - A PDF file, and one GIF image for every page will be returned.
     *                                     Each image is approximately 1190 by 1684 pixels.
     *
     * @link https://webview.webservices.nl/documentation/files/service_kadaster-php.html#Kadaster.kadasterEigendomsBerichtDocumentPostcode
     *
     * @return \stdClass <BerichtObjectDocumentResultaat>
     */
    public function kadasterEigendomsBerichtDocumentPostcode($postcode, $huisNummer, $huisNummerToevoeging, $format)
    {
        return $this->getAdapter()->call(
            'kadasterEigendomsBerichtDocumentPostcode',
            [
                'postcode' => $postcode,
                'huisnummer' => $huisNummer,
                'huisnummer_toevoeging' => $huisNummerToevoeging,
                'format' => $format,
            ]
        );
    }

    /**
     * Find a 'Eigendomsbericht' by parcel details.
     * Returns the result as a <BerichtObjectResultaat>. In addition to the structured result, a file in the PDF format
     * is returned. If the input matches more than one parcel, a list of the parcels found is returned instead. Sectie,
     * perceelnummer and the code or name of the municipality are required.
     *
     * @param string $gemeenteCode  Municipality code
     * @param string $gemeenteNaam  Municipality name
     * @param string $sectie        Section code
     * @param string $perceelnummer Parcel number
     * @param string $relatieCode   Indicates object relation type, set if object is part of another parcel. If
     *                              relatiecode is specified, volgnummer should be specified as well. Allowed values:
     *                              'A', 'D', or empty.
     * @param string $volgnummer    Object index number, set if object is part of another parcel
     *
     * @link       https://webview.webservices.nl/documentation/files/service_kadaster-php.html#Kadaster.kadasterEigendomsBerichtPerceel
     * @deprecated please use <kadasterEigendomsBerichtPerceelV2> instead
     *
     * @return \stdClass <BerichtObjectResultaat>
     */
    public function kadasterEigendomsBerichtPerceel(
        $gemeenteCode,
        $gemeenteNaam,
        $sectie,
        $perceelnummer,
        $relatieCode,
        $volgnummer
    ) {
        return $this->getAdapter()->call(
            'kadasterEigendomsBerichtPerceel',
            [
                'gemeentecode' => $gemeenteCode,
                'gemeentenaam' => $gemeenteNaam,
                'sectie' => $sectie,
                'perceelnummer' => $perceelnummer,
                'relatiecode' => $relatieCode,
                'volgnummer' => $volgnummer,
            ]
        );
    }

    /**
     * Find a 'Eigendomsbericht' by parcel details.
     * Returns the result as a <BerichtObjectResultaatV2>. In addition to the structured result, a file in the PDF
     * format is returned. If the input matches more than one parcel, a list of the parcels found is returned instead.
     * Sectie, perceelnummer and the code or name of the municipality are required.
     *
     * @param string $gemeenteCode  Municipality code
     * @param string $gemeenteNaam  Municipality name. See <kadasterValueListGetMunicipalities> for possible values.
     * @param string $sectie        Section code
     * @param string $perceelnummer Parcel number
     * @param string $relatieCode   Indicates object relation type, set if object is part of another parcel. If
     *                              relatiecode is specified, volgnummer should be specified as well. Allowed values:
     *                              'A', 'D', or empty.
     * @param string $volgnummer    Object index number, set if object is part of another parcel
     *
     * @link  https://webview.webservices.nl/documentation/files/service_kadaster-php.html#Kadaster.kadasterEigendomsBerichtPerceelV2
     *
     * @return \stdClass <BerichtObjectResultaatV2>
     */
    public function kadasterEigendomsBerichtPerceelV2(
        $gemeenteCode,
        $gemeenteNaam,
        $sectie,
        $perceelnummer,
        $relatieCode,
        $volgnummer
    ) {
        return $this->getAdapter()->call(
            'kadasterEigendomsBerichtPerceelV2',
            [
                'gemeentecode' => $gemeenteCode,
                'gemeentenaam' => $gemeenteNaam,
                'sectie' => $sectie,
                'perceelnummer' => $perceelnummer,
                'relatiecode' => $relatieCode,
                'volgnummer' => $volgnummer,
            ]
        );
    }

    /**
     * Find a 'Eigendomsbericht' by postcode and house number.
     * Returns the result as a <BerichtObjectResultaat>. In addition to the structured result, a file in the PDF format
     * is returned. If the input matches more than one parcel, a list of the parcels found is returned instead.
     *
     * @param string $postcode             Address postcode
     * @param int    $huisNummer           Address house number
     * @param string $huisNummerToevoeging Address house number addition
     *
     * @link       https://webview.webservices.nl/documentation/files/service_kadaster-php.html#Kadaster.kadasterEigendomsBerichtPostcode
     * @deprecated please use kadasterEigendomsBerichtPostcodeV2
     *
     * @return \stdClass <BerichtObjectResultaat>
     */
    public function kadasterEigendomsBerichtPostcode($postcode, $huisNummer, $huisNummerToevoeging)
    {
        return $this->getAdapter()->call(
            'kadasterEigendomsBerichtPostcode',
            [
                'postcode' => $postcode,
                'huisnummer' => $huisNummer,
                'huisnummer_toevoeging' => $huisNummerToevoeging,
            ]
        );
    }

    /**
     * Find a 'Eigendomsbericht' by postcode and house number.
     * In addition to the structured result, a file in the PDF format is returned. If the input matches more than one
     * parcel, a list of the parcels found is returned instead.
     *
     * @param string $postcode             Address postcode
     * @param int    $huisNummer           Address house number
     * @param string $huisNummerToevoeging Address house number addition
     *
     * @link  https://webview.webservices.nl/documentation/files/service_kadaster-php.html#Kadaster.kadasterEigendomsBerichtPostcodeV2
     *
     * @return \stdClass <BerichtObjectResultaatV2>
     */
    public function kadasterEigendomsBerichtPostcodeV2($postcode, $huisNummer, $huisNummerToevoeging)
    {
        return $this->getAdapter()->call(
            'kadasterEigendomsBerichtPostcodeV2',
            [
                'postcode' => $postcode,
                'huisnummer' => $huisNummer,
                'huisnummer_toevoeging' => $huisNummerToevoeging,
            ]
        );
    }

    /**
     * Find a 'Hypothecair bericht' by parcel details.
     * If one parcel is found, the "hypothecairbericht" field of the <kadasterHypothecairBerichtResultaat> contains
     * information about that specific parcel. If more parcels match, the "overzicht" field contains information about
     * all the parcels the requested parcel has been divided in, or transferred into. The result will always be encoded
     * in base 64. If an image format is requested a conversion is performed on our servers, which might cause the
     * response to be delayed for large documents. We recommend using a longer timeout setting for such requests.
     *
     * @param string $gemeenteCode  Municipality code
     * @param string $gemeenteNaam  Municipality name
     * @param string $sectie        Section code
     * @param string $perceelnummer Parcel number
     * @param string $relatieCode   Indicates object relation type, set if object is part of another parcel. If
     *                              relatiecode is specified, volgnummer should be specified as well.
     *                              Allowed values: 'A', 'D', or empty.
     * @param string $volgnummer    Object index number, set if object is part of another parcel
     * @param string $format        File type of the result. Supported formats:
     *                              none- No document will be returned.
     *                              pdf - Only a PDF document will be returned.
     *                              png_16 - A PDF file, and one PNG image for every page will be returned.
     *                              Each image is approximately 132 by 187 pixels.
     *                              png_144 - A PDF file, and one PNG image for every page will be returned.
     *                              Each image is approximately 1190 by 1684 pixels.
     *                              gif_144 -- A PDF file, and one GIF image for every page will be returned.
     *                              Each image is approximately 1190 by 1684 pixels.
     *
     * @deprecated please use <kadasterHypothecairBerichtPerceelV3>
     *
     * @return \stdClass <kadasterHypothecairBerichtResultaat>
     */
    public function kadasterHypothecairBerichtPerceel(
        $gemeenteCode,
        $gemeenteNaam,
        $sectie,
        $perceelnummer,
        $relatieCode,
        $volgnummer,
        $format
    ) {
        return $this->getAdapter()->call(
            'kadasterHypothecairBerichtPerceel',
            [
                'gemeentecode' => $gemeenteCode,
                'gemeentenaam' => $gemeenteNaam,
                'sectie' => $sectie,
                'perceelnummer' => $perceelnummer,
                'relatiecode' => $relatieCode,
                'volgnummer' => $volgnummer,
                'format' => $format,
            ]
        );
    }

    /**
     * @param $gemeenteCode
     * @param $gemeenteNaam
     * @param $sectie
     * @param $perceelnummer
     * @param $relatieCode
     * @param $volgnummer
     * @param $format
     *
     * @return \stdClass
     */
    public function kadasterHypothecairBerichtPerceelV2(
        $gemeenteCode,
        $gemeenteNaam,
        $sectie,
        $perceelnummer,
        $relatieCode,
        $volgnummer,
        $format
    ) {
        return $this->getAdapter()->call(
            'kadasterHypothecairBerichtPerceelV2',
            [
                'gemeentecode' => $gemeenteCode,
                'gemeentenaam' => $gemeenteNaam,
                'sectie' => $sectie,
                'perceelnummer' => $perceelnummer,
                'relatiecode' => $relatieCode,
                'volgnummer' => $volgnummer,
                'format' => $format,
            ]
        );
    }

    /**
     * @param $gemeenteCode
     * @param $gemeenteNaam
     * @param $sectie
     * @param $perceelnummer
     * @param $relatieCode
     * @param $volgnummer
     * @param $format
     *
     * @return \stdClass
     */
    public function kadasterHypothecairBerichtPerceelV3(
        $gemeenteCode,
        $gemeenteNaam,
        $sectie,
        $perceelnummer,
        $relatieCode,
        $volgnummer,
        $format
    ) {
        return $this->getAdapter()->call(
            'kadasterHypothecairBerichtPerceelV3',
            [
                'gemeentecode' => $gemeenteCode,
                'gemeentenaam' => $gemeenteNaam,
                'sectie' => $sectie,
                'perceelnummer' => $perceelnummer,
                'relatiecode' => $relatieCode,
                'volgnummer' => $volgnummer,
                'format' => $format,
            ]
        );
    }

    /**
     * @param $postcode
     * @param $huisNummer
     * @param $huisNummerToevoeging
     * @param $format
     *
     * @return \stdClass
     */
    public function kadasterHypothecairBerichtPostcode($postcode, $huisNummer, $huisNummerToevoeging, $format)
    {
        return $this->getAdapter()->call(
            'kadasterHypothecairBerichtPostcode',
            [
                'postcode' => $postcode,
                'huisnummer' => $huisNummer,
                'huisnummer_toevoeging' => $huisNummerToevoeging,
                'format' => $format,
            ]
        );
    }

    /**
     * @param $postcode
     * @param $huisNummer
     * @param $huisNummerToevoeging
     * @param $format
     *
     * @return \stdClass
     */
    public function kadasterHypothecairBerichtPostcodeV2($postcode, $huisNummer, $huisNummerToevoeging, $format)
    {
        return $this->getAdapter()->call(
            'kadasterHypothecairBerichtPostcodeV2',
            [
                'postcode' => $postcode,
                'huisnummer' => $huisNummer,
                'huisnummer_toevoeging' => $huisNummerToevoeging,
                'format' => $format,
            ]
        );
    }

    /**
     * @param $postcode
     * @param $huisNummer
     * @param $huisNummerToevoeging
     * @param $format
     *
     * @return \stdClass
     */
    public function kadasterHypothecairBerichtPostcodeV3($postcode, $huisNummer, $huisNummerToevoeging, $format)
    {
        return $this->getAdapter()->call(
            'kadasterHypothecairBerichtPostcodeV3',
            [
                'postcode' => $postcode,
                'huisnummer' => $huisNummer,
                'huisnummer_toevoeging' => $huisNummerToevoeging,
                'format' => $format,
            ]
        );
    }

    /**
     * @param $gemeenteCode
     * @param $gemeenteNaam
     * @param $sectie
     * @param $perceelnummer
     * @param $relatieCode
     * @param $volgnummer
     * @param $format
     * @param $schaal
     *
     * @return \stdClass
     */
    public function kadasterKadastraleKaartPerceel(
        $gemeenteCode,
        $gemeenteNaam,
        $sectie,
        $perceelnummer,
        $relatieCode,
        $volgnummer,
        $format,
        $schaal
    ) {
        return $this->getAdapter()->call(
            'kadasterKadastraleKaartPerceel',
            [
                'gemeentecode' => $gemeenteCode,
                'gemeentenaam' => $gemeenteNaam,
                'sectie' => $sectie,
                'perceelnummer' => $perceelnummer,
                'relatiecode' => $relatieCode,
                'volgnummer' => $volgnummer,
                'format' => $format,
                'schaal' => $schaal,
            ]
        );
    }

    /**
     * @param $gemeenteCode
     * @param $gemeenteNaam
     * @param $sectie
     * @param $perceelnummer
     * @param $relatieCode
     * @param $volgnummer
     * @param $format
     * @param $schaal
     *
     * @return \stdClass
     */
    public function kadasterKadastraleKaartPerceelV2(
        $gemeenteCode,
        $gemeenteNaam,
        $sectie,
        $perceelnummer,
        $relatieCode,
        $volgnummer,
        $format,
        $schaal
    ) {
        return $this->getAdapter()->call(
            'kadasterKadastraleKaartPerceelV2',
            [
                'gemeentecode' => $gemeenteCode,
                'gemeentenaam' => $gemeenteNaam,
                'sectie' => $sectie,
                'perceelnummer' => $perceelnummer,
                'relatiecode' => $relatieCode,
                'volgnummer' => $volgnummer,
                'format' => $format,
                'schaal' => $schaal,
            ]
        );
    }

    /**
     * @param $postcode
     * @param $huisNummer
     * @param $huisNummerToevoeging
     * @param $format
     * @param $schaal
     *
     * @return \stdClass
     */
    public function kadasterKadastraleKaartPostcode($postcode, $huisNummer, $huisNummerToevoeging, $format, $schaal)
    {
        return $this->getAdapter()->call(
            'kadasterKadastraleKaartPostcode',
            [
                'postcode' => $postcode,
                'huisnummer' => $huisNummer,
                'huisnummer_toevoeging' => $huisNummerToevoeging,
                'format' => $format,
                'schaal' => $schaal,
            ]
        );
    }

    /**
     * @param $postcode
     * @param $huisNummer
     * @param $huisNummerToevoeging
     * @param $format
     * @param $schaal
     *
     * @return \stdClass
     */
    public function kadasterKadastraleKaartPostcodeV2($postcode, $huisNummer, $huisNummerToevoeging, $format, $schaal)
    {
        return $this->getAdapter()->call(
            'kadasterKadastraleKaartPostcodeV2',
            [
                'postcode' => $postcode,
                'huisnummer' => $huisNummer,
                'huisnummer_toevoeging' => $huisNummerToevoeging,
                'format' => $format,
                'schaal' => $schaal,
            ]
        );
    }

    /**
     * @param $postcode
     * @param $huisNummer
     *
     * @return \stdClass
     */
    public function kadasterKoopsommenOverzicht($postcode, $huisNummer)
    {
        return $this->getAdapter()->call(
            'kadasterKoopsommenOverzicht',
            [
                'postcode' => $postcode,
                'huisnummer' => $huisNummer,
            ]
        );
    }

    /**
     * @param $postcode
     * @param $huisNummer
     * @param $format
     *
     * @return \stdClass
     */
    public function kadasterKoopsommenOverzichtV2($postcode, $huisNummer, $format)
    {
        return $this->getAdapter()->call(
            'kadasterKoopsommenOverzichtV2',
            [
                'postcode' => $postcode,
                'huisnummer' => $huisNummer,
                'format' => $format,
            ]
        );
    }

    /**
     * @param $gemeenteCode
     * @param $gemeenteNaam
     * @param $sectie
     * @param $perceelnummer
     * @param $relatieCode
     * @param $volgnummer
     * @param $format
     *
     * @return \stdClass
     */
    public function kadasterUittrekselKadastraleKaartPerceel(
        $gemeenteCode,
        $gemeenteNaam,
        $sectie,
        $perceelnummer,
        $relatieCode,
        $volgnummer,
        $format
    ) {
        return $this->getAdapter()->call(
            'kadasterUittrekselKadastraleKaartPerceel',
            [
                'gemeentecode' => $gemeenteCode,
                'gemeentenaam' => $gemeenteNaam,
                'sectie' => $sectie,
                'perceelnummer' => $perceelnummer,
                'relatiecode' => $relatieCode,
                'volgnummer' => $volgnummer,
                'format' => $format,
            ]
        );
    }

    /**
     * @param $gemeenteCode
     * @param $gemeenteNaam
     * @param $sectie
     * @param $perceelnummer
     * @param $relatieCode
     * @param $volgnummer
     * @param $format
     *
     * @return \stdClass
     */
    public function kadasterUittrekselKadastraleKaartPerceelV2(
        $gemeenteCode,
        $gemeenteNaam,
        $sectie,
        $perceelnummer,
        $relatieCode,
        $volgnummer,
        $format
    ) {
        return $this->getAdapter()->call(
            'kadasterUittrekselKadastraleKaartPerceelV2',
            [
                'gemeentecode' => $gemeenteCode,
                'gemeentenaam' => $gemeenteNaam,
                'sectie' => $sectie,
                'perceelnummer' => $perceelnummer,
                'relatiecode' => $relatieCode,
                'volgnummer' => $volgnummer,
                'format' => $format,
            ]
        );
    }

    /**
     * @param $gemeenteCode
     * @param $gemeenteNaam
     * @param $sectie
     * @param $perceelnummer
     * @param $relatieCode
     * @param $volgnummer
     * @param $format
     *
     * @return \stdClass
     */
    public function kadasterUittrekselKadastraleKaartPerceelV3(
        $gemeenteCode,
        $gemeenteNaam,
        $sectie,
        $perceelnummer,
        $relatieCode,
        $volgnummer,
        $format
    ) {
        return $this->getAdapter()->call(
            'kadasterUittrekselKadastraleKaartPerceelV3',
            [
                'gemeentecode' => $gemeenteCode,
                'gemeentenaam' => $gemeenteNaam,
                'sectie' => $sectie,
                'perceelnummer' => $perceelnummer,
                'relatiecode' => $relatieCode,
                'volgnummer' => $volgnummer,
                'format' => $format,
            ]
        );
    }

    /**
     * @param $postcode
     * @param $huisNummer
     * @param $huisNummerToevoeging
     * @param $format
     *
     * @return \stdClass
     */
    public function kadasterUittrekselKadastraleKaartPostcode($postcode, $huisNummer, $huisNummerToevoeging, $format)
    {
        return $this->getAdapter()->call(
            'kadasterUittrekselKadastraleKaartPostcode',
            [
                'postcode' => $postcode,
                'huisnummer' => $huisNummer,
                'huisnummer_toevoeging' => $huisNummerToevoeging,
                'format' => $format,
            ]
        );
    }

    /**
     * @param $postcode
     * @param $huisNummer
     * @param $huisNummerToevoeging
     * @param $format
     *
     * @return \stdClass
     */
    public function kadasterUittrekselKadastraleKaartPostcodeV2($postcode, $huisNummer, $huisNummerToevoeging, $format)
    {
        return $this->getAdapter()->call(
            'kadasterUittrekselKadastraleKaartPostcodeV2',
            [
                'postcode' => $postcode,
                'huisnummer' => $huisNummer,
                'huisnummer_toevoeging' => $huisNummerToevoeging,
                'format' => $format,
            ]
        );
    }

    /**
     * @param $postcode
     * @param $huisNummer
     * @param $huisNummerToevoeging
     * @param $format
     *
     * @return \stdClass
     */
    public function kadasterUittrekselKadastraleKaartPostcodeV3($postcode, $huisNummer, $huisNummerToevoeging, $format)
    {
        return $this->getAdapter()->call(
            'kadasterUittrekselKadastraleKaartPostcodeV3',
            [
                'postcode' => $postcode,
                'huisnummer' => $huisNummer,
                'huisnummer_toevoeging' => $huisNummerToevoeging,
                'format' => $format,
            ]
        );
    }

    /**
     * @return \stdClass
     */
    public function kadasterValueListGetMunicipalities()
    {
        return $this->getAdapter()->call('kadasterValueListGetMunicipalities', []);
    }

    /**
     * @return \stdClass
     */
    public function kadasterValueListGetRanges()
    {
        return $this->getAdapter()->call('kadasterValueListGetRanges', []);
    }

    /**
     * @param $dossierNumber
     * @param $establishmentNumber
     *
     * @return \stdClass
     */
    public function kvkGetDossier($dossierNumber, $establishmentNumber)
    {
        return $this->getAdapter()->call(
            'kvkGetDossier',
            [
                'dossier_number' => $dossierNumber,
                'establishment_number' => $establishmentNumber,
            ]
        );
    }

    /**
     * @param $dossierNumber
     * @param $allowCaching
     *
     * @return \stdClass
     */
    public function kvkGetExtractDocument($dossierNumber, $allowCaching)
    {
        return $this->getAdapter()->call(
            'kvkGetExtractDocument',
            ['dossier_number' => $dossierNumber, 'allow_caching' => $allowCaching]
        );
    }

    /**
     * @param $dossierNumber
     * @param $establishmentNumber
     * @param $rsinNumber
     * @param $page
     *
     * @return \stdClass
     */
    public function kvkSearchDossierNumber($dossierNumber, $establishmentNumber, $rsinNumber, $page)
    {
        return $this->getAdapter()->call(
            'kvkSearchDossierNumber',
            [
                'dossier_number' => $dossierNumber,
                'establishment_number' => $establishmentNumber,
                'rsin_number' => $rsinNumber,
                'page' => $page,
            ]
        );
    }

    /**
     * @param $tradeName
     * @param $city
     * @param $street
     * @param $postcode
     * @param $houseNumber
     * @param $houseNumberAddition
     * @param $telephoneNumber
     * @param $domainName
     * @param $strictSearch
     * @param $page
     *
     * @return \stdClass
     */
    public function kvkSearchParameters(
        $tradeName,
        $city,
        $street,
        $postcode,
        $houseNumber,
        $houseNumberAddition,
        $telephoneNumber,
        $domainName,
        $strictSearch,
        $page
    ) {
        return $this->getAdapter()->call(
            'kvkSearchParameters',
            [
                'trade_name' => $tradeName,
                'city' => $city,
                'street' => $street,
                'postcode' => $postcode,
                'house_number' => $houseNumber,
                'house_number_addition' => $houseNumberAddition,
                'telephone_number' => $telephoneNumber,
                'domain_name' => $domainName,
                'strict_search' => $strictSearch,
                'page' => $page,
            ]
        );
    }

    /**
     * @param $postcode
     * @param $houseNumber
     * @param $houseNumberAddition
     * @param $page
     *
     * @return \stdClass
     */
    public function kvkSearchPostcode($postcode, $houseNumber, $houseNumberAddition, $page)
    {
        return $this->getAdapter()->call(
            'kvkSearchPostcode',
            [
                'postcode' => $postcode,
                'house_number' => $houseNumber,
                'house_number_addition' => $houseNumberAddition,
                'page' => $page,
            ]
        );
    }

    /**
     * @param $city
     * @param $postcode
     * @param $sbi
     * @param $primarySbiOnly
     * @param $legalForm
     * @param $employeesMin
     * @param $employeesMax
     * @param $economicallyActive
     * @param $financialStatus
     * @param $changedSince
     * @param $newSince
     * @param $page
     *
     * @return \stdClass
     */
    public function kvkSearchSelection(
        $city,
        $postcode,
        $sbi,
        $primarySbiOnly,
        $legalForm,
        $employeesMin,
        $employeesMax,
        $economicallyActive,
        $financialStatus,
        $changedSince,
        $newSince,
        $page
    ) {
        return $this->getAdapter()->call(
            'kvkSearchSelection',
            [
                'city' => $city,
                'postcode' => $postcode,
                'sbi' => $sbi,
                'primary_sbi_only' => $primarySbiOnly,
                'legal_form' => $legalForm,
                'employees_min' => $employeesMin,
                'employees_max' => $employeesMax,
                'economically_active' => $economicallyActive,
                'financial_status' => $financialStatus,
                'changed_since' => $changedSince,
                'new_since' => $newSince,
                'page' => $page,
            ]
        );
    }

    /**
     * @param $dossierNumber
     * @param $establishmentNumber
     *
     * @return \stdClass
     */
    public function kvkUpdateAddDossier($dossierNumber, $establishmentNumber)
    {
        return $this->getAdapter()->call(
            'kvkUpdateAddDossier',
            ['dossier_number' => $dossierNumber, 'establishment_number' => $establishmentNumber]
        );
    }

    /**
     * @param $dossierNumber
     * @param $establishmentNumber
     * @param $updateTypes
     *
     * @return \stdClass
     */
    public function kvkUpdateCheckDossier($dossierNumber, $establishmentNumber, $updateTypes)
    {
        return $this->getAdapter()->call(
            'kvkUpdateCheckDossier',
            [
                'dossier_number' => $dossierNumber,
                'establishment_number' => $establishmentNumber,
                'update_types' => $updateTypes,
            ]
        );
    }

    /**
     * @param $changedSince
     * @param $updateTypes
     * @param $page
     *
     * @return \stdClass
     */
    public function kvkUpdateGetChangedDossiers($changedSince, $updateTypes, $page)
    {
        return $this->getAdapter()->call(
            'kvkUpdateGetChangedDossiers',
            ['changed_since' => $changedSince, 'update_types' => $updateTypes, 'page' => $page]
        );
    }

    /**
     * @param $updateTypes
     * @param $page
     *
     * @return \stdClass
     */
    public function kvkUpdateGetDossiers($updateTypes, $page)
    {
        return $this->getAdapter()->call('kvkUpdateGetDossiers', ['update_types' => $updateTypes, 'page' => $page]);
    }

    /**
     * @param $dossierNumber
     * @param $establishmentNumber
     *
     * @return \stdClass
     */
    public function kvkUpdateRemoveDossier($dossierNumber, $establishmentNumber)
    {
        return $this->getAdapter()->call(
            'kvkUpdateRemoveDossier',
            ['dossier_number' => $dossierNumber, 'establishment_number' => $establishmentNumber]
        );
    }

    /**
     * Login.
     *
     * @param string $username
     * @param string $password
     *
     * @return \stdClass
     */
    public function login($username, $password)
    {
        return $this->getAdapter()->call('login', ['username' => $username, 'password' => $password]);
    }

    /**
     * End the session of the current user.
     */
    public function logout()
    {
        return $this->getAdapter()->call('logout', []);
    }

    /**
     * @param $latitude
     * @param $longitude
     * @param $format
     * @param $width
     * @param $height
     * @param $zoom
     *
     * @return \stdClass
     */
    public function mapViewInternationalLatLon($latitude, $longitude, $format, $width, $height, $zoom)
    {
        return $this->getAdapter()->call(
            'mapViewInternationalLatLon',
            [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'format' => $format,
                'width' => $width,
                'height' => $height,
                'zoom' => $zoom,
            ]
        );
    }

    /**
     * @param $centerLat
     * @param $centerLon
     * @param $extraLatLon
     * @param $format
     * @param $width
     * @param $height
     * @param $zoom
     *
     * @return \stdClass
     */
    public function mapViewLatLon($centerLat, $centerLon, $extraLatLon, $format, $width, $height, $zoom)
    {
        return $this->getAdapter()->call(
            'mapViewLatLon',
            [
                'center_lat' => $centerLat,
                'center_lon' => $centerLon,
                'extra_latlon' => $extraLatLon,
                'format' => $format,
                'width' => $width,
                'height' => $height,
                'zoom' => $zoom,
            ]
        );
    }

    /**
     * @param $postcode
     * @param $format
     * @param $width
     * @param $height
     * @param $zoom
     *
     * @return \stdClass
     */
    public function mapViewPostcodeV2($postcode, $format, $width, $height, $zoom)
    {
        return $this->getAdapter()->call(
            'mapViewPostcodeV2',
            [
                'postcode' => $postcode,
                'format' => $format,
                'width' => $width,
                'height' => $height,
                'zoom' => $zoom,
            ]
        );
    }

    /**
     * Returns a map in PNG or JPG format centered on the xy RD coordinate.
     * The extra_xy parameter can be used to specify additional locations, the map is not centered or zoomed to
     * automatically enclose these locations.
     *
     * @param int    $centerX The RD X component of the coordinate
     * @param int    $centerY The RD Y component of the coordinate
     * @param array  $extraXY Additional RDCoordinates, an <Patterns::{Type}Array> of type <RDCoordinates>
     * @param string $format  Image format, PNG (default) or JPG
     * @param int    $width   Width in pixels, domain [1 - 2048]
     * @param int    $height  Height in pixels, domain [1 - 2048]
     * @param float  $zoom    Scale in meters per pixel. See: <Zoom>
     *
     * @return \stdClass
     */
    public function mapViewRD($centerX, $centerY, $extraXY, $format, $width, $height, $zoom)
    {
        return $this->getAdapter()->call(
            'mapViewRD',
            [
                'center_x' => $centerX,
                'center_y' => $centerY,
                'extra_xy' => $extraXY,
                'format' => $format,
                'width' => $width,
                'height' => $height,
                'zoom' => $zoom,
            ]
        );
    }

    /**
     * Returns a value estimate for the real estate at the specified address.
     * The required parameters are: postcode, houseno and testing_date. The remaining parameters are retrieved from the
     * Kadaster if available. If those parameters are specified in the request they override any Kadaster data, and will
     * be used in the calculation of the value estimate. If no value estimate can be determined the response will be an
     * error with error code (see <Error Handling::Error codes>) 'Server.Data.NotFound.Nbwo.EstimateUnavailable'.
     * Type of houses:
     * A -- Appartment
     * H -- Corner house (Hoekwoning)
     * K -- Semi detached house (Twee onder een kap)
     * N -- Not a house
     * O -- Unknown type of house
     * T -- Townhouse (Tussingwoning)
     * V -- Detached house (Vrijstaande woning).
     *
     * @param string $postcode             Postalcode
     * @param int    $huisNummer           House number
     * @param string $huisNummerToevoeging House number addition, may be left empty
     * @param string $prijspeilDatum       Date for which the value should be determined, in the format Y-m-d
     * @param string $woningtype           house type, may be empty
     * @param int    $inhoud               Volume in cubic meters, may be empty (0)
     * @param int    $grootte              Surface area of the parcel in square meters, may be empty (0)
     *
     * @return \stdClass
     */
    public function nbwoEstimateValue(
        $postcode,
        $huisNummer,
        $huisNummerToevoeging,
        $prijspeilDatum,
        $woningtype,
        $inhoud,
        $grootte
    ) {
        return $this->getAdapter()->call(
            'nbwoEstimateValue',
            [
                'postcode' => $postcode,
                'huisnummer' => $huisNummer,
                'huisnummer_toevoeging' => $huisNummerToevoeging,
                'prijspeil_datum' => $prijspeilDatum,
                'woningtype' => $woningtype,
                'inhoud' => $inhoud,
                'grootte' => $grootte,
            ]
        );
    }

    /**
     * @param $gender
     * @param $initials
     * @param $prefix
     * @param $lastName
     * @param $birthDate
     * @param $street
     * @param $houseNumber
     * @param $houseNumberAddition
     * @param $postcode
     * @param $city
     *
     * @return \stdClass
     */
    public function riskCheckGetRiskPersonCompanyReport(
        $gender,
        $initials,
        $prefix,
        $lastName,
        $birthDate,
        $street,
        $houseNumber,
        $houseNumberAddition,
        $postcode,
        $city
    ) {
        return $this->getAdapter()->call(
            'riskCheckGetRiskPersonCompanyReport',
            [
                'gender' => $gender,
                'initials' => $initials,
                'prefix' => $prefix,
                'last_name' => $lastName,
                'birth_date' => $birthDate,
                'street' => $street,
                'house_number' => $houseNumber,
                'house_number_addition' => $houseNumberAddition,
                'postcode' => $postcode,
                'city' => $city,
            ]
        );
    }

    /**
     * Returns a score indicating creditworthiness for a Dutch person, address and postcode area.
     * The given parameters are used to search for a person.
     *   The following fields are tried in the order listed, until a matching person is found:
     *   1 - initials, name, postcode, house number
     *   2 - initials, name, birth date
     *   3 - postcode, house number, birth date
     *   4 - account number, birth date
     *   5 - phone number, birth date
     *   6 - mobile number, birth date
     *   7 - email, birth date
     * For instance, if initials, postcode, house number and birth date are specified and a match is found on the
     * fields
     * listed under 1, birth date will be ignored. Scores for address and postcode are determined independent of the
     * person details. Search fields are case-insensitive. Non-ASCII characters are mapped to the corresponding
     * character without diacritical mark (e.g. an accented e is mapped to an 'e').
     *
     * @param string $gender              Gender of the person. M or F, may be empty.
     * @param string $initials            the initials, mandatory
     * @param string $prefix              the surname prefix, like "van" or "de", may be empty
     * @param string $lastName            the last name of the person, mandatory
     * @param string $birthDate           birth date in the format yyyy-mm-dd, may be empty
     * @param string $street              street part of the address, may be empty
     * @param int    $houseNumber         house number, mandatory
     * @param string $houseNumberAddition extension part of the house number, may be empty
     * @param string $postcode            dutch postcode in the format 1234AB, mandatory
     * @param string $city                city, may be empty
     * @param string $accountNumber       bank account number, only numeric characters, may be empty
     * @param string $phoneNumber         Home phone number, only numeric characters (e.g. 0201234567), may be empty
     * @param string $mobileNumber        Mobile phone number, only numeric characters (e.g. 0612345678), may be empty
     * @param string $email               email address, may be empty
     * @param string $testingDate         date for which the credit score should be determined, format Y-m-d, mandatory
     *
     * @return \stdClass
     */
    public function riskCheckPerson(
        $gender,
        $initials,
        $prefix,
        $lastName,
        $birthDate,
        $street,
        $houseNumber,
        $houseNumberAddition,
        $postcode,
        $city,
        $accountNumber,
        $phoneNumber,
        $mobileNumber,
        $email,
        $testingDate
    ) {
        return $this->getAdapter()->call(
            'riskCheckPerson',
            [
                'gender' => $gender,
                'initials' => $initials,
                'prefix' => $prefix,
                'last_name' => $lastName,
                'birth_date' => $birthDate,
                'street' => $street,
                'house_number' => $houseNumber,
                'house_number_addition' => $houseNumberAddition,
                'postcode' => $postcode,
                'city' => $city,
                'account_number' => $accountNumber,
                'phone_number' => $phoneNumber,
                'mobile_number' => $mobileNumber,
                'email' => $email,
                'testing_date' => $testingDate,
            ]
        );
    }

    /**
     * Returns a description of the fastest route between two dutch postcodes.
     * For every part of the route the drivetime in seconds and drivedistance in meters are given as well. The
     * description is available in dutch and english, depending on the english parameter toggle.
     *
     * @param string $postcodeFrom Postcode at the start of the route
     * @param string $postcodeTo   Postcode at the end of the route
     * @param bool   $english      Whether to returns the description in english (true) or Dutch (false)
     *
     * @return \stdClass
     */
    public function routePlannerDescription($postcodeFrom, $postcodeTo, $english)
    {
        return $this->getAdapter()->call(
            'routePlannerDescription',
            [
                'postcodefrom' => $postcodeFrom,
                'postcodeto' => $postcodeTo,
                'english' => $english,
            ]
        );
    }

    /**
     * Returns a description of the route calculated between two addresses.
     * For every part of the route the drive time and drive distance are given. The description is available in several
     * languages controlled by the language parameter. The fastest, most economic or shortest route can be calculated.
     *
     * @param string $routeType    Type of route to calculate, 'fastest', 'shortest' or 'economic'
     * @param string $toPostalCode Start address postal code
     * @param string $fromHouseNo  Start address house number
     * @param string $fromStreet   Start address street
     * @param string $fromCity     Start address city
     * @param string $fromCountry  Start country (ISO3, ISO2 or Full-Text)
     * @param string $toPostalcode Destination address postal code
     * @param string $toHouseNo    Destination address house-number
     * @param string $toStreet     Destination address street
     * @param string $toCity       Destination address city
     * @param string $toCountry    Destination country (ISO3, ISO2 or Full-Text)
     * @param string $language     'danish', 'dutch', 'english', 'french', 'german' or 'italian'
     *
     * @return \stdClass
     */
    public function routePlannerDescriptionAddress(
        $routeType,
        $toPostalCode,
        $fromHouseNo,
        $fromStreet,
        $fromCity,
        $fromCountry,
        $toPostalcode,
        $toHouseNo,
        $toStreet,
        $toCity,
        $toCountry,
        $language
    ) {
        return $this->getAdapter()->call(
            'routePlannerDescriptionAddress',
            [
                'routetype' => $routeType,
                'from_postalcode' => $toPostalCode,
                'from_houseno' => $fromHouseNo,
                'from_street' => $fromStreet,
                'from_city' => $fromCity,
                'from_country' => $fromCountry,
                'to_postalcode' => $toPostalcode,
                'to_houseno' => $toHouseNo,
                'to_street' => $toStreet,
                'to_city' => $toCity,
                'to_country' => $toCountry,
                'language' => $language,
            ]
        );
    }

    /**
     * Returns a description of the route between two dutch postcodes, including the RD coordinates along the route.
     * For every part of the route the drive time in seconds and drive distance in meters are given as well.
     * The route type can be shortest, economic or fastest. By default the fastest route will be calculated.
     * The description is available in dutch and english, depending on the english parameter toggle.
     *
     * @param string $postcodeFrom Postcode at the start of the route
     * @param string $postcodeTo   Postcode at the end of the route
     * @param string $routeType    Type of route to calculate: 'fastest', 'shortest' or 'economic'
     * @param bool   $english      Whether to returns the description in english (true) or dutch (false)
     *
     * @return \stdClass <RouteDescriptionCoordinatesRD> entry
     */
    public function routePlannerDescriptionCoordinatesRD($postcodeFrom, $postcodeTo, $routeType, $english)
    {
        return $this->getAdapter()->call(
            'routePlannerDescriptionCoordinatesRD',
            [
                'postcodefrom' => $postcodeFrom,
                'postcodeto' => $postcodeTo,
                'routetype' => $routeType,
                'english' => $english,
            ]
        );
    }

    /**
     * Returns a description of the route calculated between two dutch addresses.
     * For every part of the route the drive time and drive distance are given as well. The description is available in
     * several languages depending on the language parameter. The fastest, most economic or shortest route can be
     * calculated.
     *
     * @param string $routeType      Type of route to calculate, 'fastest', 'shortest' or 'economic'
     * @param string $fromPostalCode Start address postal code
     * @param string $fromHousNo     Start address house number
     * @param string $fromStreet     Start address street
     * @param string $fromCity       Start address city
     * @param string $toPostalCode   Destination address postal code
     * @param string $toHousNo       Destination address house-number
     * @param string $toStreet       Destination address street
     * @param string $toCity         Destination address city
     * @param string $language       Language description: 'danish', 'dutch', 'english', 'french', 'german' or 'italian'
     *
     * @return \stdClass <Patterns::{Type}Array> of <RoutePart> entries
     */
    public function routePlannerDescriptionDutchAddress(
        $routeType,
        $fromPostalCode,
        $fromHousNo,
        $fromStreet,
        $fromCity,
        $toPostalCode,
        $toHousNo,
        $toStreet,
        $toCity,
        $language
    ) {
        return $this->getAdapter()->call(
            'routePlannerDescriptionDutchAddress',
            [
                'routetype' => $routeType,
                'from_postalcode' => $fromPostalCode,
                'from_housno' => $fromHousNo,
                'from_street' => $fromStreet,
                'from_city' => $fromCity,
                'to_postalcode' => $toPostalCode,
                'to_housno' => $toHousNo,
                'to_street' => $toStreet,
                'to_city' => $toCity,
                'language' => $language,
            ]
        );
    }

    /**
     * @param $postcodeFrom
     * @param $postcodeTo
     * @param $english
     *
     * @return \stdClass
     */
    public function routePlannerDescriptionShortest($postcodeFrom, $postcodeTo, $english)
    {
        return $this->getAdapter()->call(
            'routePlannerDescriptionShortest',
            ['postcodefrom' => $postcodeFrom, 'postcodeto' => $postcodeTo, 'english' => $english]
        );
    }

    /**
     * Returns a description of the route between two latitude/longitude coordinates in Europe.
     * For every part of the route the drive time and drive distance are given as well. The description is available in
     * several languages depending on the language parameter. The fastest, economic or shortest route is calculated.
     *
     * @param float  $latitudeFrom  Latitude of the start of the route
     * @param float  $longitudeFrom Longitude of the start of the route
     * @param float  $latitudeTo    Latitude of the end of the route
     * @param float  $longitudeTo   Longitude of the end of the route
     * @param float  $routeType     Type of route to calculate: 'fastest', 'shortest' or 'economic'
     * @param string $language      'danish', 'dutch', 'english', 'french', 'german', 'italian' or 'swedish'
     *
     * @return \stdClass <Patterns::{Type}Array> of <RoutePart> entries
     */
    public function routePlannerEUDescription(
        $latitudeFrom,
        $longitudeFrom,
        $latitudeTo,
        $longitudeTo,
        $routeType,
        $language
    ) {
        return $this->getAdapter()->call(
            'routePlannerEUDescription',
            [
                'latitudefrom' => (float) $latitudeFrom,
                'longitudefrom' => (float) $longitudeFrom,
                'latitudeto' => (float) $latitudeTo,
                'longitudeto' => (float) $longitudeTo,
                'routetype' => (float) $routeType,
                'language' => $language,
            ]
        );
    }

    /**
     * @param $latitudeFrom
     * @param $longitudeFrom
     * @param $latitudeTo
     * @param $longitudeTo
     * @param $routeType
     * @param $language
     *
     * @return \stdClass
     */
    public function routePlannerEUDescriptionCoordinatesLatLon(
        $latitudeFrom,
        $longitudeFrom,
        $latitudeTo,
        $longitudeTo,
        $routeType,
        $language
    ) {
        return $this->getAdapter()->call(
            'routePlannerEUDescriptionCoordinatesLatLon',
            [
                'latitudefrom' => $latitudeFrom,
                'longitudefrom' => $longitudeFrom,
                'latitudeto' => $latitudeTo,
                'longitudeto' => $longitudeTo,
                'routetype' => $routeType,
                'language' => $language,
            ]
        );
    }

    /**
     * @param $latitudeFrom
     * @param $longitudeFrom
     * @param $latitudeTo
     * @param $longitudeTo
     * @param $routeType
     *
     * @return \stdClass
     */
    public function routePlannerEUInformation(
        $latitudeFrom,
        $longitudeFrom,
        $latitudeTo,
        $longitudeTo,
        $routeType
    ) {
        return $this->getAdapter()->call(
            'routePlannerEUInformation',
            [
                'latitudefrom' => $latitudeFrom,
                'longitudefrom' => $longitudeFrom,
                'latitudeto' => $latitudeTo,
                'longitudeto' => $longitudeTo,
                'routetype' => $routeType,
            ]
        );
    }

    /**
     * @param $latitudeFrom
     * @param $longitudeFrom
     * @param $latitudeTo
     * @param $longitudeTo
     * @param $routeType
     * @param $language
     * @param $format
     * @param $width
     * @param $height
     * @param $view
     *
     * @return \stdClass
     */
    public function routePlannerEUMap(
        $latitudeFrom,
        $longitudeFrom,
        $latitudeTo,
        $longitudeTo,
        $routeType,
        $language,
        $format,
        $width,
        $height,
        $view
    ) {
        return $this->getAdapter()->call(
            'routePlannerEUMap',
            [
                'latitudefrom' => $latitudeFrom,
                'longitudefrom' => $longitudeFrom,
                'latitudeto' => $latitudeTo,
                'longitudeto' => $longitudeTo,
                'routetype' => $routeType,
                'language' => $language,
                'format' => $format,
                'width' => $width,
                'height' => $height,
                'view' => $view,
            ]
        );
    }

    /**
     * @param $startPostcode
     * @param $startHouseNumber
     * @param $startHouseNumberAddition
     * @param $startStreet
     * @param $startCity
     * @param $startCountry
     * @param $destinationPostcode
     * @param $destinationHouseNumber
     * @param $destinationHouseNumberAddition
     * @param $destinationStreet
     * @param $destinationCity
     * @param $destinationCountry
     * @param $routeType
     * @param $language
     *
     * @return \stdClass
     */
    public function routePlannerGetRoute(
        $startPostcode,
        $startHouseNumber,
        $startHouseNumberAddition,
        $startStreet,
        $startCity,
        $startCountry,
        $destinationPostcode,
        $destinationHouseNumber,
        $destinationHouseNumberAddition,
        $destinationStreet,
        $destinationCity,
        $destinationCountry,
        $routeType,
        $language
    ) {
        return $this->getAdapter()->call(
            'routePlannerGetRoute',
            [
                'start_postcode' => $startPostcode,
                'start_house_number' => $startHouseNumber,
                'start_house_number_addition' => $startHouseNumberAddition,
                'start_street' => $startStreet,
                'start_city' => $startCity,
                'start_country' => $startCountry,
                'destination_postcode' => $destinationPostcode,
                'destination_house_number' => $destinationHouseNumber,
                'destination_house_number_addition' => $destinationHouseNumberAddition,
                'destination_street' => $destinationStreet,
                'destination_city' => $destinationCity,
                'destination_country' => $destinationCountry,
                'route_type' => $routeType,
                'language' => $language,
            ]
        );
    }

    /**
     * @param $postcodeFrom
     * @param $postcodeTo
     * @param $routeType
     *
     * @return \stdClass
     */
    public function routePlannerInformation($postcodeFrom, $postcodeTo, $routeType)
    {
        return $this->getAdapter()->call(
            'routePlannerInformation',
            [
                'postcodefrom' => $postcodeFrom,
                'postcodeto' => $postcodeTo,
                'routetype' => $routeType,
            ]
        );
    }

    /**
     * @param $routeType
     * @param $fromPostalCode
     * @param $fromHouseNo
     * @param $fromStreet
     * @param $fromCity
     * @param $fromCountry
     * @param $toPostalCode
     * @param $toHouseNo
     * @param $toStreet
     * @param $toCity
     * @param $toCountry
     *
     * @return \stdClass
     */
    public function routePlannerInformationAddress(
        $routeType,
        $fromPostalCode,
        $fromHouseNo,
        $fromStreet,
        $fromCity,
        $fromCountry,
        $toPostalCode,
        $toHouseNo,
        $toStreet,
        $toCity,
        $toCountry
    ) {
        return $this->getAdapter()->call(
            'routePlannerInformationAddress',
            [
                'routetype' => $routeType,
                'from_postalcode' => $fromPostalCode,
                'from_houseno' => $fromHouseNo,
                'from_street' => $fromStreet,
                'from_city' => $fromCity,
                'from_country' => $fromCountry,
                'to_postalcode' => $toPostalCode,
                'to_houseno' => $toHouseNo,
                'to_street' => $toStreet,
                'to_city' => $toCity,
                'to_country' => $toCountry,
            ]
        );
    }

    /**
     * @param $routeType
     * @param $toPostalCode
     * @param $fromHousNo
     * @param $fromStreet
     * @param $fromCity
     * @param $toPostalcode
     * @param $toHousNo
     * @param $toStreet
     * @param $toCity
     *
     * @return \stdClass
     */
    public function routePlannerInformationDutchAddress(
        $routeType,
        $toPostalCode,
        $fromHousNo,
        $fromStreet,
        $fromCity,
        $toPostalcode,
        $toHousNo,
        $toStreet,
        $toCity
    ) {
        return $this->getAdapter()->call(
            'routePlannerInformationDutchAddress',
            [
                'routetype' => $routeType,
                'from_postalcode' => $toPostalCode,
                'from_housno' => $fromHousNo,
                'from_street' => $fromStreet,
                'from_city' => $fromCity,
                'to_postalcode' => $toPostalcode,
                'to_housno' => $toHousNo,
                'to_street' => $toStreet,
                'to_city' => $toCity,
            ]
        );
    }

    /**
     * @param $xFrom
     * @param $yFrom
     * @param $xTo
     * @param $yTo
     * @param $routeType
     * @param $english
     *
     * @return \stdClass
     */
    public function routePlannerRDDescription($xFrom, $yFrom, $xTo, $yTo, $routeType, $english)
    {
        return $this->getAdapter()->call(
            'routePlannerRDDescription',
            [
                'xfrom' => $xFrom,
                'yfrom' => $yFrom,
                'xto' => $xTo,
                'yto' => $yTo,
                'routetype' => $routeType,
                'english' => $english,
            ]
        );
    }

    /**
     * @param $xFrom
     * @param $yFrom
     * @param $xTo
     * @param $yTo
     * @param $routeType
     * @param $english
     *
     * @return \stdClass
     */
    public function routePlannerRDDescriptionCoordinatesRD($xFrom, $yFrom, $xTo, $yTo, $routeType, $english)
    {
        return $this->getAdapter()->call(
            'routePlannerRDDescriptionCoordinatesRD',
            [
                'xfrom' => $xFrom,
                'yfrom' => $yFrom,
                'xto' => $xTo,
                'yto' => $yTo,
                'routetype' => $routeType,
                'english' => $english,
            ]
        );
    }

    /**
     * @param $xFrom
     * @param $yFrom
     * @param $xTo
     * @param $yTo
     * @param $routeType
     *
     * @return \stdClass
     */
    public function routePlannerRDInformation($xFrom, $yFrom, $xTo, $yTo, $routeType)
    {
        return $this->getAdapter()->call(
            'routePlannerRDInformation',
            [
                'xfrom' => $xFrom,
                'yfrom' => $yFrom,
                'xto' => $xTo,
                'yto' => $yTo,
                'routetype' => $routeType,
            ]
        );
    }

    /**
     * @param $bban
     * @param $countryIso
     *
     * @return \stdClass
     */
    public function sepaConvertBasicBankAccountNumber($bban, $countryIso)
    {
        return $this->getAdapter()->call(
            'sepaConvertBasicBankAccountNumber',
            ['bban' => $bban, 'country_iso' => $countryIso]
        );
    }

    /**
     * @param $iban
     *
     * @return \stdClass
     */
    public function sepaValidateInternationalBankAccountNumberFormat($iban)
    {
        return $this->getAdapter()->call('sepaValidateInternationalBankAccountNumberFormat', ['iban' => $iban]);
    }

    /**
     * Add a user to a group. A user can use <userListAssignableGroups> to view the groups that can be assigned.
     *
     * @param int $userId      User ID of the user to add to the group, use 0 for the current user
     * @param int $userGroupId User Group ID of the group to add the user to
     *
     * @return \stdClass
     */
    public function userAddGroup($userId, $userGroupId)
    {
        return $this->getAdapter()->call('userAddGroup', ['userid' => $userId, 'usergroupid' => $userGroupId]);
    }

    /**
     * @param int    $userId
     * @param string $oldPassword
     * @param string $newPassword
     *
     * @return \stdClass
     */
    public function userChangePassword($userId, $oldPassword, $newPassword)
    {
        return $this->getAdapter()->call(
            'userChangePassword',
            [
                'userid' => $userId,
                'old_password' => $oldPassword,
                'new_password' => $newPassword,
            ]
        );
    }

    /**
     * Create a user, assign it to groups and send it an activation mail.
     * Together with the activation email, this is the only time the password is plainly visible.
     *
     * @param int    $accountId    Account ID to assign this user to
     * @param string $nickname     Nickname to use for this user, leave empty to to create a random nickname. All users
     *                             get a prefix set by <Account::Username prefix>
     * @param string $password     password to use for authentication, leave empty for a strong random password
     * @param array  $userGroups   array of usergroup IDs to assign this user to. See <userListAssignableGroups> for
     *                             list
     * @param string $email        Registration email address, used for activation
     * @param string $companyName  Name of the company using this user, if any
     * @param string $address      Address of the company using this user, if any
     * @param string $contactName  Name of the contact person responsible for this user
     * @param string $contactEmail this field is not used and is ignored by the method
     * @param string $telephone    Telephone number of the contact person responsible for this user
     * @param string $fax          fax number of the contact person responsible for this user
     * @param string $clientCode   Deprecated, should contain an empty string
     * @param string $comments     Comments on the user, can only be seen and edited by <Group::Account admins>
     *
     * @link http://webview.webservices.nl/documentation/files/service_accounting-class-php.html#Accounting.userCreateV2
     *
     * @return \stdClass
     */
    public function userCreateV2(
        $accountId,
        $nickname,
        $password,
        $userGroups,
        $email,
        $companyName,
        $address,
        $contactName,
        $contactEmail,
        $telephone,
        $fax,
        $clientCode,
        $comments
    ) {
        return $this->getAdapter()->call(
            'userCreateV2',
            [
                'accountid' => $accountId,
                'nickname' => $nickname,
                'password' => $password,
                'usergroups' => $userGroups,
                'email' => $email,
                'companyname' => $companyName,
                'address' => $address,
                'contactname' => $contactName,
                'contactemail' => $contactEmail,
                'telephone' => $telephone,
                'fax' => $fax,
                'clientcode' => $clientCode,
                'comments' => $comments,
            ]
        );
    }

    /**
     * Change the user's balance.
     *
     * @param int $userId  ID of the user to edit the balance of, use 0 for the current user
     * @param int $balance Amount of balance to add to (or remove from, if negative) the user
     *
     * @return \stdClass
     */
    public function userEditBalance($userId, $balance)
    {
        return $this->getAdapter()->call('userEditBalance', ['userid' => $userId, 'balance' => $balance]);
    }

    /**
     * Edit the complete profile of a user.
     * This method is only available to <Group::Account admins>. <Group::Account users> can use <userEditV2>
     * to change some part of the profile.
     *
     * @param int    $userId                 User ID of the user to edit, use 0 for the current user
     * @param string $nickname               Nickname to use for this user. All users get a prefix set by
     *                                       <Account::Username prefix>.
     * @param string $password               new password for this user. To keep the current password pass the empty
     *                                       string.
     * @param string $email                  registration email address, used for activation
     * @param string $companyName            name of the company using this user, if any
     * @param string $address                Address of the company using this user, if any
     * @param string $contactName            Name of the contact person responsible for this user
     * @param string $contactEmail           Telephone number of the contact person responsible for this user
     * @param string $telephone              Telephone number of the contact person responsible for this user
     * @param string $fax                    fax number of the contact person responsible for this user
     * @param string $clientCode             Deprecated, shoud contain an empty string
     * @param string $comments               comments on the user, can only be seen and edited by <Group::Account
     *                                       admins>
     * @param int    $accountId              accountID to assign user to, use 0 for current account. Only usable by
     *                                       <Group::Admins>
     * @param int    $balanceThreshold       balance threshold to alert user, 0 to disable
     * @param string $notificationRecipients Recipients of balance alert notification:
     *                                       'accountcontact' = contact account contact, 'user' = contact user,
     *                                       'accountcontact_and_user' = both
     *
     * @return \stdClass
     */
    public function userEditExtendedV2(
        $userId,
        $nickname,
        $password,
        $email,
        $companyName,
        $address,
        $contactName,
        $contactEmail,
        $telephone,
        $fax,
        $clientCode,
        $comments,
        $accountId,
        $balanceThreshold,
        $notificationRecipients
    ) {
        return $this->getAdapter()->call('userEditExtendedV2', [
            $userId,
            $nickname,
            $password,
            $email,
            $companyName,
            $address,
            $contactName,
            $contactEmail,
            $telephone,
            $fax,
            $clientCode,
            $comments,
            $accountId,
            $balanceThreshold,
            $notificationRecipients,
        ]);
    }

    /**
     * Set host restrictions for the user.
     *
     * @param int    $userId
     * @param string $restrictions string with host restrictions separated by semi colons (;)
     *
     * @return \stdClass
     */
    public function userEditHostRestrictions($userId, $restrictions)
    {
        return $this->getAdapter()->call(
            'userEditHostRestrictions',
            ['userid' => $userId, 'restrictions' => $restrictions]
        );
    }

    /**
     * List all groups that the current user can assign to the target user.
     * This list contains both assigned and unassigned groups.
     *
     * @param int $userId User ID of the user to target, use 0 for the current user
     * @param int $page   Page to retrieve, pages start counting at 1
     *
     * @return \stdClass
     */
    public function userListAssignableGroups($userId, $page)
    {
        return $this->getAdapter()->call('userListAssignableGroups', ['userid' => $userId, 'page' => $page]);
    }

    /**
     * Send a notification email to a user with a new password. This method is part of the <User::Creation> process.
     *
     * @param int    $userId   User ID of the user to notify, use 0 for the current user
     * @param string $password password to use for authentication, leave empty for a strong random password
     *
     * @return \stdClass
     */
    public function userNotify($userId, $password)
    {
        return $this->getAdapter()->call('userNotify', ['userid' => $userId, 'password' => $password]);
    }

    /**
     * @param int $userId
     *
     * @return \stdClass
     */
    public function userRemove($userId)
    {
        return $this->getAdapter()->call('userRemove', ['userid' => $userId]);
    }

    /**
     * Remove a user from a group. A user can use <userViewV2> to view the groups that are currently assigned to user.
     *
     * @param int $userId      - User ID of the user to remove from the group, use 0 for the current user
     * @param int $userGroupId - User Group ID of the group to remove the user from
     *
     * @return \stdClass
     */
    public function userRemoveGroup($userId, $userGroupId)
    {
        return $this->getAdapter()->call('userRemoveGroup', ['userid' => $userId, 'usergroupid' => $userGroupId]);
    }

    /**
     * Lists all the current valid <User::Sessions> of a <User>.
     *
     * @param int $userId User ID of the user to view, use 0 for the current user
     * @param int $page   Page to retrieve, pages start counting at 1
     *
     * @return \stdClass <Patterns::{Type}PagedResult> of <Session> entries
     */
    public function userSessionList($userId, $page)
    {
        return $this->getAdapter()->call('userSessionList', ['userid' => $userId, 'page' => $page]);
    }

    /**
     * Remove all or one <User::Session> of a <User>.
     *
     * @param int $userId  ID of the user to view, use 0 for the current user
     * @param int $reactId Session ID to remove, use 0 to remove all sessions
     *
     * @return \stdClass
     */
    public function userSessionRemove($userId, $reactId)
    {
        return $this->getAdapter()->call('userSessionRemove', ['userid' => $userId, 'reactid' => $reactId]);
    }

    /**
     * Returns the users balance.
     * If the user is in the 'autoassign' user group, he is not restricted by his balance. In that case, he can still do
     * method calls even though his balance amount is zero. If the user is not in the 'autoassign' user group, the user
     * can spend his own balance amount, but not more.
     *
     * @param int $userId ID of the user to view the balance of, use 0 for the current user
     *
     * @return int
     */
    public function userViewBalance($userId)
    {
        return $this->getAdapter()->call('userViewBalance', ['userid' => $userId]);
    }

    /**
     * View host restrictions for the user.
     *
     * @param int $userId User ID of the user, use 0 for the current user
     *
     * @return string String containing all restrictions, separated by  semi colons
     */
    public function userViewHostRestrictions($userId)
    {
        return $this->getAdapter()->call('userViewHostRestrictions', ['userid' => $userId]);
    }

    /**
     * View the profile of a user.
     *
     * @param int $userId Id of the user to view, use 0 for the current user
     *
     * @return array
     */
    public function userViewV2($userId = 0)
    {
        return $this->getAdapter()->call('userViewV2', ['userid' => $userId]);
    }

    /**
     * @param $vatNumber
     *
     * @return \stdClass
     */
    public function vatValidate($vatNumber)
    {
        return $this->getAdapter()->call('vatValidate', ['vat_number' => $vatNumber]);
    }

    /**
     * @param $vatNumber
     *
     * @return \stdClass
     */
    public function vatViesProxyCheckVat($vatNumber)
    {
        return $this->getAdapter()->call('vatViesProxyCheckVat', ['vat_number' => $vatNumber]);
    }
}
