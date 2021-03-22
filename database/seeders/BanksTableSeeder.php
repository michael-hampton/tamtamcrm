<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BanksTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('banks')->delete();
        
        DB::table('banks')->insert(array (
            0 => 
            array (
                'remote_id' => '421',
            'name' => 'ING DIRECT (Canada)',
                'config' => '{"fid":"061400152","org":"INGDirectCanada","url":"https:\\/\\/ofx.ingdirect.ca"}',
                'id' => 1,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:38',
                'updated_at' => '2021-01-18 23:24:38',
            ),
            1 => 
            array (
                'remote_id' => '422',
                'name' => 'Safe Credit Union - OFXDownload Beta',
                'config' => '{"fid":"321173742","org":"DI","url":"https:\\/\\/ofxcert.diginsite.com\\/cmr\\/cmr.ofx"}',
                'id' => 2,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:38',
                'updated_at' => '2021-01-18 23:24:38',
            ),
            2 => 
            array (
                'remote_id' => '423',
                'name' => 'Ascentra Credit Union',
                'config' => '{"fid":"273973456","org":"Alcoa Employees&Community CU","url":"https:\\/\\/alc.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 3,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:38',
                'updated_at' => '2021-01-18 23:24:38',
            ),
            3 => 
            array (
                'remote_id' => '424',
                'name' => 'American Express Card',
                'config' => '{"fid":"3101","org":"AMEX","url":"https:\\/\\/online.americanexpress.com\\/myca\\/ofxdl\\/desktop\\/desktopDownload.do?request_type=nl_ofxdownload"}',
                'id' => 4,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:38',
                'updated_at' => '2021-01-18 23:24:38',
            ),
            4 => 
            array (
                'remote_id' => '425',
                'name' => 'TD Ameritrade',
                'config' => '{"fid":"5024","org":"ameritrade.com","url":"https:\\/\\/ofxs.ameritrade.com\\/cgi-bin\\/apps\\/OFXDownload"}',
                'id' => 5,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:38',
                'updated_at' => '2021-01-18 23:24:38',
            ),
            5 => 
            array (
                'remote_id' => '426',
                'name' => 'Truliant FCU',
                'config' => '{"fid":"253177832","org":"DI","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                'id' => 6,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:39',
                'updated_at' => '2021-01-18 23:24:39',
            ),
            6 => 
            array (
                'remote_id' => '427',
                'name' => 'AT&T Universal Card',
                'config' => '{"fid":"24909","org":"Citigroup","url":"https:\\/\\/secureofx2.bankhost.com\\/citi\\/cgi-forte\\/ofx_rt?servicename=ofx_rt&pagename=ofx"}',
                'id' => 7,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:39',
                'updated_at' => '2021-01-18 23:24:39',
            ),
            7 => 
            array (
                'remote_id' => '428',
                'name' => 'Bank One',
                'config' => '{"fid":"5811","org":"B1","url":"https:\\/\\/onlineofx.chase.com\\/chase.ofx"}',
                'id' => 8,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:39',
                'updated_at' => '2021-01-18 23:24:39',
            ),
            8 => 
            array (
                'remote_id' => '429',
                'name' => 'Bank of Stockton',
                'config' => '{"fid":"3901","org":"BOS","url":"https:\\/\\/internetbanking.bankofstockton.com\\/scripts\\/serverext.dll"}',
                'id' => 9,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:39',
                'updated_at' => '2021-01-18 23:24:39',
            ),
            9 => 
            array (
                'remote_id' => '430',
                'name' => 'Bank of the Cascades',
                'config' => '{"fid":"4751","org":"JackHenry","url":"https:\\/\\/directline.netteller.com"}',
                'id' => 10,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:39',
                'updated_at' => '2021-01-18 23:24:39',
            ),
            10 => 
            array (
                'remote_id' => '431',
                'name' => 'Centra Credit Union',
                'config' => '{"fid":"274972883","org":"Centra CU","url":"https:\\/\\/centralink.org\\/scripts\\/isaofx.dll"}',
                'id' => 11,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:40',
                'updated_at' => '2021-01-18 23:24:40',
            ),
            11 => 
            array (
                'remote_id' => '432',
                'name' => 'Centura Bank',
                'config' => '{"fid":"1901","org":"Centura Bank","url":"https:\\/\\/www.oasis.cfree.com\\/1901.ofxgp"}',
                'id' => 12,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:40',
                'updated_at' => '2021-01-18 23:24:40',
            ),
            12 => 
            array (
                'remote_id' => '433',
                'name' => 'Charles Schwab&Co., INC',
                'config' => '{"fid":"5104","org":"ISC","url":"https:\\/\\/ofx.schwab.com\\/cgi_dev\\/ofx_server"}',
                'id' => 13,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:40',
                'updated_at' => '2021-01-18 23:24:40',
            ),
            13 => 
            array (
                'remote_id' => '434',
            'name' => 'JPMorgan Chase Bank (Texas)',
                'config' => '{"fid":"5301","org":"Chase Bank of Texas","url":"https:\\/\\/www.oasis.cfree.com\\/5301.ofxgp"}',
                'id' => 14,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:40',
                'updated_at' => '2021-01-18 23:24:40',
            ),
            14 => 
            array (
                'remote_id' => '435',
                'name' => 'JPMorgan Chase Bank',
                'config' => '{"fid":"1601","org":"Chase Bank","url":"https:\\/\\/www.oasis.cfree.com\\/1601.ofxgp"}',
                'id' => 15,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:40',
                'updated_at' => '2021-01-18 23:24:40',
            ),
            15 => 
            array (
                'remote_id' => '436',
                'name' => 'Colonial Bank',
                'config' => '{"fid":"1046","org":"Colonial Banc Group","url":"https:\\/\\/www.oasis.cfree.com\\/1046.ofxgp"}',
                'id' => 16,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:40',
                'updated_at' => '2021-01-18 23:24:40',
            ),
            16 => 
            array (
                'remote_id' => '437',
                'name' => 'Comerica Bank',
                'config' => '{"fid":"5601","org":"Comerica","url":"https:\\/\\/www.oasis.cfree.com\\/5601.ofxgp"}',
                'id' => 17,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:40',
                'updated_at' => '2021-01-18 23:24:40',
            ),
            17 => 
            array (
                'remote_id' => '438',
                'name' => 'Commerce Bank NJ, PA, NY&DE',
                'config' => '{"fid":"1001","org":"CommerceBank","url":"https:\\/\\/www.commerceonlinebanking.com\\/scripts\\/serverext.dll"}',
                'id' => 18,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:40',
                'updated_at' => '2021-01-18 23:24:40',
            ),
            18 => 
            array (
                'remote_id' => '439',
                'name' => 'Commerce Bank, NA',
                'config' => '{"fid":"4001","org":"Commerce Bank NA","url":"https:\\/\\/www.oasis.cfree.com\\/4001.ofxgp"}',
                'id' => 19,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:40',
                'updated_at' => '2021-01-18 23:24:40',
            ),
            19 => 
            array (
                'remote_id' => '440',
                'name' => 'Commercial Federal Bank',
                'config' => '{"fid":"4801","org":"CommercialFederalBank","url":"https:\\/\\/www.oasis.cfree.com\\/4801.ofxgp"}',
                'id' => 20,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:40',
                'updated_at' => '2021-01-18 23:24:40',
            ),
            20 => 
            array (
                'remote_id' => '441',
                'name' => 'COMSTAR FCU',
                'config' => '{"fid":"255074988","org":"Comstar Federal Credit Union","url":"https:\\/\\/pcu.comstarfcu.org\\/scripts\\/isaofx.dll"}',
                'id' => 21,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:40',
                'updated_at' => '2021-01-18 23:24:40',
            ),
            21 => 
            array (
                'remote_id' => '442',
                'name' => 'SunTrust',
                'config' => '{"fid":"2801","org":"SunTrust PC Banking","url":"https:\\/\\/www.oasis.cfree.com\\/2801.ofxgp"}',
                'id' => 22,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:41',
                'updated_at' => '2021-01-18 23:24:41',
            ),
            22 => 
            array (
                'remote_id' => '443',
                'name' => 'Denali Alaskan FCU',
                'config' => '{"fid":"1","org":"Denali Alaskan FCU","url":"https:\\/\\/remotebanking.denalifcu.com\\/ofx\\/ofx.dll"}',
                'id' => 23,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:41',
                'updated_at' => '2021-01-18 23:24:41',
            ),
            23 => 
            array (
                'remote_id' => '444',
                'name' => 'Discover Card',
                'config' => '{"fid":"7101","org":"Discover Financial Services","url":"https:\\/\\/ofx.discovercard.com"}',
                'id' => 24,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:41',
                'updated_at' => '2021-01-18 23:24:41',
            ),
            24 => 
            array (
                'remote_id' => '446',
                'name' => 'E*TRADE',
                'config' => '{"fid":"fldProv_mProvBankId","org":"fldProv_mId","url":"https:\\/\\/ofx.etrade.com\\/cgi-ofx\\/etradeofx"}',
                'id' => 25,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:41',
                'updated_at' => '2021-01-18 23:24:41',
            ),
            25 => 
            array (
                'remote_id' => '447',
                'name' => 'Eastern Bank',
                'config' => '{"fid":"6201","org":"Eastern Bank","url":"https:\\/\\/www.oasis.cfree.com\\/6201.ofxgp"}',
                'id' => 26,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:41',
                'updated_at' => '2021-01-18 23:24:41',
            ),
            26 => 
            array (
                'remote_id' => '448',
                'name' => 'EDS Credit Union',
                'config' => '{"fid":"311079474","org":"EDS CU","url":"https:\\/\\/eds.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 27,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:41',
                'updated_at' => '2021-01-18 23:24:41',
            ),
            27 => 
            array (
                'remote_id' => '449',
                'name' => 'Fidelity Investments',
                'config' => '{"fid":"7776","org":"fidelity.com","url":"https:\\/\\/ofx.fidelity.com\\/ftgw\\/OFXDownload\\/clients\\/download"}',
                'id' => 28,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:41',
                'updated_at' => '2021-01-18 23:24:41',
            ),
            28 => 
            array (
                'remote_id' => '450',
                'name' => 'Fifth Third Bancorp',
                'config' => '{"fid":"5829","org":"Fifth Third Bank","url":"https:\\/\\/banking.53.com\\/ofx\\/OFXServlet"}',
                'id' => 29,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:41',
                'updated_at' => '2021-01-18 23:24:41',
            ),
            29 => 
            array (
                'remote_id' => '451',
                'name' => 'First Tech Credit Union',
                'config' => '{"fid":"2243","org":"First Tech Credit Union","url":"https:\\/\\/ofx.firsttechcu.com"}',
                'id' => 30,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:41',
                'updated_at' => '2021-01-18 23:24:41',
            ),
            30 => 
            array (
                'remote_id' => '452',
                'name' => 'zWachovia',
                'config' => '{"fid":"4301","org":"Wachovia","url":"https:\\/\\/www.oasis.cfree.com\\/4301.ofxgp"}',
                'id' => 31,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:41',
                'updated_at' => '2021-01-18 23:24:41',
            ),
            31 => 
            array (
                'remote_id' => '453',
                'name' => 'KeyBank',
                'config' => '{"fid":"5901","org":"KeyBank","url":"https:\\/\\/www.oasis.cfree.com\\/fip\\/genesis\\/prod\\/05901.ofx"}',
                'id' => 32,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:41',
                'updated_at' => '2021-01-18 23:24:41',
            ),
            32 => 
            array (
                'remote_id' => '454',
                'name' => 'Mellon Bank',
                'config' => '{"fid":"1226","org":"Mellon Bank","url":"https:\\/\\/www.oasis.cfree.com\\/1226.ofxgp"}',
                'id' => 33,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:41',
                'updated_at' => '2021-01-18 23:24:41',
            ),
            33 => 
            array (
                'remote_id' => '455',
                'name' => 'LaSalle Bank Midwest',
                'config' => '{"fid":"1101","org":"LaSalleBankMidwest","url":"https:\\/\\/www.oasis.cfree.com\\/1101.ofxgp"}',
                'id' => 34,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:41',
                'updated_at' => '2021-01-18 23:24:41',
            ),
            34 => 
            array (
                'remote_id' => '456',
                'name' => 'Nantucket Bank',
                'config' => '{"fid":"466","org":"Nantucket","url":"https:\\/\\/ofx.onlinencr.com\\/scripts\\/serverext.dll"}',
                'id' => 35,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:41',
                'updated_at' => '2021-01-18 23:24:41',
            ),
            35 => 
            array (
                'remote_id' => '457',
                'name' => 'National Penn Bank',
                'config' => '{"fid":"6301","org":"National Penn Bank","url":"https:\\/\\/www.oasis.cfree.com\\/6301.ofxgp"}',
                'id' => 36,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:41',
                'updated_at' => '2021-01-18 23:24:41',
            ),
            36 => 
            array (
                'remote_id' => '458',
                'name' => 'Nevada State Bank - New',
                'config' => '{"fid":"1121","org":"295-3","url":"https:\\/\\/quicken.metavante.com\\/ofx\\/OFXServlet"}',
                'id' => 37,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:41',
                'updated_at' => '2021-01-18 23:24:41',
            ),
            37 => 
            array (
                'remote_id' => '459',
                'name' => 'UBS Financial Services Inc.',
                'config' => '{"fid":"7772","org":"Intuit","url":"https:\\/\\/ofx1.ubs.com\\/eftxweb\\/access.ofx"}',
                'id' => 38,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:41',
                'updated_at' => '2021-01-18 23:24:41',
            ),
            38 => 
            array (
                'remote_id' => '460',
                'name' => 'Patelco CU',
                'config' => '{"fid":"2000","org":"Patelco Credit Union","url":"https:\\/\\/ofx.patelco.org"}',
                'id' => 39,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:41',
                'updated_at' => '2021-01-18 23:24:41',
            ),
            39 => 
            array (
                'remote_id' => '461',
                'name' => 'Mercantile Brokerage Services',
                'config' => '{"fid":"011","org":"Mercantile Brokerage","url":"https:\\/\\/ofx.netxclient.com\\/cgi\\/OFXNetx"}',
                'id' => 40,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:41',
                'updated_at' => '2021-01-18 23:24:41',
            ),
            40 => 
            array (
                'remote_id' => '462',
                'name' => 'Regions Bank',
                'config' => '{"fid":"243","org":"regions.com","url":"https:\\/\\/ofx.morgankeegan.com\\/begasp\\/directtocore.asp"}',
                'id' => 41,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:41',
                'updated_at' => '2021-01-18 23:24:41',
            ),
            41 => 
            array (
                'remote_id' => '463',
                'name' => 'Spectrum Connect/Reich&Tang',
                'config' => '{"fid":"6510","org":"SpectrumConnect","url":"https:\\/\\/www.oasis.cfree.com\\/6510.ofxgp"}',
                'id' => 42,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:41',
                'updated_at' => '2021-01-18 23:24:41',
            ),
            42 => 
            array (
                'remote_id' => '464',
                'name' => 'Smith Barney - Transactions',
                'config' => '{"fid":"3201","org":"SmithBarney","url":"https:\\/\\/www.oasis.cfree.com\\/3201.ofxgp"}',
                'id' => 43,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:41',
                'updated_at' => '2021-01-18 23:24:41',
            ),
            43 => 
            array (
                'remote_id' => '465',
                'name' => 'Southwest Airlines FCU',
                'config' => '{"fid":"311090673","org":"Southwest Airlines EFCU","url":"https:\\/\\/www.swacuflashbp.org\\/scripts\\/isaofx.dll"}',
                'id' => 44,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:42',
                'updated_at' => '2021-01-18 23:24:42',
            ),
            44 => 
            array (
                'remote_id' => '467',
                'name' => 'Technology Credit Union - CA',
                'config' => '{"fid":"11257","org":"Tech CU","url":"https:\\/\\/webbranchofx.techcu.com\\/TekPortalOFX\\/servlet\\/TP_OFX_Controller"}',
                'id' => 45,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:42',
                'updated_at' => '2021-01-18 23:24:42',
            ),
            45 => 
            array (
                'remote_id' => '468',
                'name' => 'UMB Bank',
                'config' => '{"fid":"0","org":"UMB","url":"https:\\/\\/pcbanking.umb.com\\/hs_ofx\\/hsofx.dll"}',
                'id' => 46,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:42',
                'updated_at' => '2021-01-18 23:24:42',
            ),
            46 => 
            array (
                'remote_id' => '469',
                'name' => 'Union Bank of California',
                'config' => '{"fid":"2901","org":"Union Bank of California","url":"https:\\/\\/www.oasis.cfree.com\\/2901.ofxgp"}',
                'id' => 47,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:42',
                'updated_at' => '2021-01-18 23:24:42',
            ),
            47 => 
            array (
                'remote_id' => '470',
                'name' => 'United Teletech Financial',
                'config' => '{"fid":"221276011","org":"DI","url":"https:\\/\\/ofxcore.digitalinsight.com:443\\/servlet\\/OFXCoreServlet"}',
                'id' => 48,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:42',
                'updated_at' => '2021-01-18 23:24:42',
            ),
            48 => 
            array (
                'remote_id' => '471',
                'name' => 'US Bank',
                'config' => '{"fid":"1401","org":"US Bank","url":"https:\\/\\/www.oasis.cfree.com\\/1401.ofxgp"}',
                'id' => 49,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:42',
                'updated_at' => '2021-01-18 23:24:42',
            ),
            49 => 
            array (
                'remote_id' => '472',
            'name' => 'Bank of America (All except CA, WA,&ID)',
                'config' => '{"fid":"6812","org":"HAN","url":"https:\\/\\/ofx.bankofamerica.com\\/cgi-forte\\/fortecgi?servicename=ofx_2-3&pagename=ofx"}',
                'id' => 50,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:42',
                'updated_at' => '2021-01-18 23:24:42',
            ),
            50 => 
            array (
                'remote_id' => '473',
                'name' => 'Wells Fargo',
                'config' => '{"fid":"3000","org":"WF","url":"https:\\/\\/ofxdc.wellsfargo.com\\/ofx\\/process.ofx"}',
                'id' => 51,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:42',
                'updated_at' => '2021-01-18 23:24:42',
            ),
            51 => 
            array (
                'remote_id' => '474',
                'name' => 'LaSalle Bank NA',
                'config' => '{"fid":"6501","org":"LaSalle Bank NA","url":"https:\\/\\/www.oasis.cfree.com\\/6501.ofxgp"}',
                'id' => 52,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:42',
                'updated_at' => '2021-01-18 23:24:42',
            ),
            52 => 
            array (
                'remote_id' => '475',
                'name' => 'BB&T',
                'config' => '{"fid":"BB&T","org":"BB&T","url":"https:\\/\\/eftx.bbt.com\\/eftxweb\\/access.ofx"}',
                'id' => 53,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:42',
                'updated_at' => '2021-01-18 23:24:42',
            ),
            53 => 
            array (
                'remote_id' => '476',
                'name' => 'Los Alamos National Bank',
                'config' => '{"fid":"107001012","org":"LANB","url":"https:\\/\\/ofx.lanb.com\\/ofx\\/ofxrelay.dll"}',
                'id' => 54,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:42',
                'updated_at' => '2021-01-18 23:24:42',
            ),
            54 => 
            array (
                'remote_id' => '477',
                'name' => 'Citadel FCU',
                'config' => '{"fid":"citadel","org":"CitadelFCU","url":"https:\\/\\/pcu.citadelfcu.org\\/scripts\\/isaofx.dll"}',
                'id' => 55,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:42',
                'updated_at' => '2021-01-18 23:24:42',
            ),
            55 => 
            array (
                'remote_id' => '478',
                'name' => 'Clearview Federal Credit Union',
                'config' => '{"fid":"243083237","org":"Clearview Federal Credit Union","url":"https:\\/\\/www.pcu.clearviewfcu.org\\/scripts\\/isaofx.dll"}',
                'id' => 56,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:42',
                'updated_at' => '2021-01-18 23:24:42',
            ),
            56 => 
            array (
                'remote_id' => '479',
                'name' => 'Vanguard Group, The',
                'config' => '{"fid":"1358","org":"The Vanguard Group","url":"https:\\/\\/vesnc.vanguard.com\\/us\\/OfxDirectConnectServlet"}',
                'id' => 57,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:42',
                'updated_at' => '2021-01-18 23:24:42',
            ),
            57 => 
            array (
                'remote_id' => '480',
                'name' => 'First Citizens Bank - NC, VA, WV',
                'config' => '{"fid":"5013","org":"First Citizens Bank NC, VA, WV","url":"https:\\/\\/www.oasis.cfree.com\\/5013.ofxgp"}',
                'id' => 58,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:43',
                'updated_at' => '2021-01-18 23:24:43',
            ),
            58 => 
            array (
                'remote_id' => '481',
                'name' => 'Northern Trust - Banking',
                'config' => '{"fid":"5804","org":"ORG","url":"https:\\/\\/www3883.ntrs.com\\/nta\\/ofxservlet"}',
                'id' => 59,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:43',
                'updated_at' => '2021-01-18 23:24:43',
            ),
            59 => 
            array (
                'remote_id' => '482',
                'name' => 'The Mechanics Bank',
                'config' => '{"fid":"121102036","org":"TMB","url":"https:\\/\\/ofx.mechbank.com\\/OFXServer\\/ofxsrvr.dll"}',
                'id' => 60,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:43',
                'updated_at' => '2021-01-18 23:24:43',
            ),
            60 => 
            array (
                'remote_id' => '483',
                'name' => 'USAA Federal Savings Bank',
                'config' => '{"fid":"24591","org":"USAA","url":"https:\\/\\/service2.usaa.com\\/ofx\\/OFXServlet"}',
                'id' => 61,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:43',
                'updated_at' => '2021-01-18 23:24:43',
            ),
            61 => 
            array (
                'remote_id' => '484',
                'name' => 'Florida Telco CU',
                'config' => '{"fid":"FTCU","org":"FloridaTelcoCU","url":"https:\\/\\/ppc.floridatelco.org\\/scripts\\/isaofx.dll"}',
                'id' => 62,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:43',
                'updated_at' => '2021-01-18 23:24:43',
            ),
            62 => 
            array (
                'remote_id' => '485',
                'name' => 'DuPont Community Credit Union',
                'config' => '{"fid":"251483311","org":"DuPont Community Credit Union","url":"https:\\/\\/pcu.mydccu.com\\/scripts\\/isaofx.dll"}',
                'id' => 63,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:43',
                'updated_at' => '2021-01-18 23:24:43',
            ),
            63 => 
            array (
                'remote_id' => '486',
                'name' => 'Central Florida Educators FCU',
                'config' => '{"fid":"590678236","org":"CentralFloridaEduc","url":"https:\\/\\/www.mattweb.cfefcu.com\\/scripts\\/isaofx.dll"}',
                'id' => 64,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:43',
                'updated_at' => '2021-01-18 23:24:43',
            ),
            64 => 
            array (
                'remote_id' => '487',
                'name' => 'California Bank&Trust',
                'config' => '{"fid":"5006","org":"401","url":"https:\\/\\/pfm.metavante.com\\/ofx\\/OFXServlet"}',
                'id' => 65,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:43',
                'updated_at' => '2021-01-18 23:24:43',
            ),
            65 => 
            array (
                'remote_id' => '488',
                'name' => 'First Commonwealth FCU',
                'config' => '{"fid":"231379199","org":"FirstCommonwealthFCU","url":"https:\\/\\/pcu.firstcomcu.org\\/scripts\\/isaofx.dll"}',
                'id' => 66,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:43',
                'updated_at' => '2021-01-18 23:24:43',
            ),
            66 => 
            array (
                'remote_id' => '489',
                'name' => 'Ameriprise Financial Services, Inc.',
                'config' => '{"fid":"3102","org":"AMPF","url":"https:\\/\\/www25.ameriprise.com\\/AMPFWeb\\/ofxdl\\/us\\/download?request_type=nl_desktopdownload"}',
                'id' => 67,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:43',
                'updated_at' => '2021-01-18 23:24:43',
            ),
            67 => 
            array (
                'remote_id' => '490',
                'name' => 'AltaOne Federal Credit Union',
                'config' => '{"fid":"322274462","org":"AltaOneFCU","url":"https:\\/\\/pcu.altaone.org\\/scripts\\/isaofx.dll"}',
                'id' => 68,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:43',
                'updated_at' => '2021-01-18 23:24:43',
            ),
            68 => 
            array (
                'remote_id' => '491',
                'name' => 'A. G. Edwards and Sons, Inc.',
                'config' => '{"fid":"43-0895447","org":"A.G. Edwards","url":"https:\\/\\/ofx.agedwards.com"}',
                'id' => 69,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:43',
                'updated_at' => '2021-01-18 23:24:43',
            ),
            69 => 
            array (
                'remote_id' => '492',
                'name' => 'Educational Employees CU Fresno',
                'config' => '{"fid":"321172594","org":"Educational Employees C U","url":"https:\\/\\/www.eecuonline.org\\/scripts\\/isaofx.dll"}',
                'id' => 70,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:43',
                'updated_at' => '2021-01-18 23:24:43',
            ),
            70 => 
            array (
                'remote_id' => '493',
                'name' => 'Hawthorne Credit Union',
                'config' => '{"fid":"271979193","org":"Hawthorne Credit Union","url":"https:\\/\\/hwt.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 71,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:43',
                'updated_at' => '2021-01-18 23:24:43',
            ),
            71 => 
            array (
                'remote_id' => '494',
                'name' => 'Firstar',
                'config' => '{"fid":"1255","org":"Firstar","url":"https:\\/\\/www.oasis.cfree.com\\/1255.ofxgp"}',
                'id' => 72,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:43',
                'updated_at' => '2021-01-18 23:24:43',
            ),
            72 => 
            array (
                'remote_id' => '495',
                'name' => 'myStreetscape',
                'config' => '{"fid":"7784","org":"Fidelity","url":"https:\\/\\/ofx.ibgstreetscape.com:443"}',
                'id' => 73,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:43',
                'updated_at' => '2021-01-18 23:24:43',
            ),
            73 => 
            array (
                'remote_id' => '496',
                'name' => 'Collegedale Credit Union',
                'config' => '{"fid":"35GFA","org":"CollegedaleCU","url":"https:\\/\\/www.netit.financial-net.com\\/ofx"}',
                'id' => 74,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:43',
                'updated_at' => '2021-01-18 23:24:43',
            ),
            74 => 
            array (
                'remote_id' => '498',
                'name' => 'GCS Federal Credit Union',
                'config' => '{"fid":"281076853","org":"Granite City Steel cu","url":"https:\\/\\/pcu.mygcscu.com\\/scripts\\/isaofx.dll"}',
                'id' => 75,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:43',
                'updated_at' => '2021-01-18 23:24:43',
            ),
            75 => 
            array (
                'remote_id' => '499',
                'name' => 'Vantage Credit Union',
                'config' => '{"fid":"281081479","org":"EECU-St. Louis","url":"https:\\/\\/secure2.eecu.com\\/scripts\\/isaofx.dll"}',
                'id' => 76,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:43',
                'updated_at' => '2021-01-18 23:24:43',
            ),
            76 => 
            array (
                'remote_id' => '500',
                'name' => 'Morgan Stanley ClientServ',
                'config' => '{"fid":"1235","org":"msdw.com","url":"https:\\/\\/ofx.morganstanleyclientserv.com\\/ofx\\/ProfileMSMoney.ofx"}',
                'id' => 77,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:43',
                'updated_at' => '2021-01-18 23:24:43',
            ),
            77 => 
            array (
                'remote_id' => '501',
                'name' => 'Kennedy Space Center FCU',
                'config' => '{"fid":"263179532","org":"Kennedy Space Center FCU","url":"https:\\/\\/www.pcu.kscfcu.org\\/scripts\\/isaofx.dll"}',
                'id' => 78,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:44',
                'updated_at' => '2021-01-18 23:24:44',
            ),
            78 => 
            array (
                'remote_id' => '502',
                'name' => 'Sierra Central Credit Union',
                'config' => '{"fid":"321174770","org":"Sierra Central Credit Union","url":"https:\\/\\/www.sierracpu.com\\/scripts\\/isaofx.dll"}',
                'id' => 79,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:44',
                'updated_at' => '2021-01-18 23:24:44',
            ),
            79 => 
            array (
                'remote_id' => '503',
                'name' => 'Virginia Educators Credit Union',
                'config' => '{"fid":"251481355","org":"Virginia Educators CU","url":"https:\\/\\/www.vecumoneylink.org\\/scripts\\/isaofx.dll"}',
                'id' => 80,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:44',
                'updated_at' => '2021-01-18 23:24:44',
            ),
            80 => 
            array (
                'remote_id' => '504',
                'name' => 'Red Crown Federal Credit Union',
                'config' => '{"fid":"303986148","org":"Red Crown FCU","url":"https:\\/\\/cre.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 81,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:44',
                'updated_at' => '2021-01-18 23:24:44',
            ),
            81 => 
            array (
                'remote_id' => '505',
                'name' => 'B-M S Federal Credit Union',
                'config' => '{"fid":"221277007","org":"B-M S Federal Credit Union","url":"https:\\/\\/bms.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 82,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:44',
                'updated_at' => '2021-01-18 23:24:44',
            ),
            82 => 
            array (
                'remote_id' => '506',
                'name' => 'Fort Stewart GeorgiaFCU',
                'config' => '{"fid":"261271364","org":"Fort Stewart FCU","url":"https:\\/\\/fsg.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 83,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:44',
                'updated_at' => '2021-01-18 23:24:44',
            ),
            83 => 
            array (
                'remote_id' => '507',
                'name' => 'Northern Trust - Investments',
                'config' => '{"fid":"6028","org":"Northern Trust Investments","url":"https:\\/\\/www3883.ntrs.com\\/nta\\/ofxservlet?accounttypegroup=INV"}',
                'id' => 84,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:44',
                'updated_at' => '2021-01-18 23:24:44',
            ),
            84 => 
            array (
                'remote_id' => '508',
                'name' => 'Picatinny Federal Credit Union',
                'config' => '{"fid":"221275216","org":"Picatinny Federal Credit Union","url":"https:\\/\\/banking.picacreditunion.com\\/scripts\\/isaofx.dll"}',
                'id' => 85,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:44',
                'updated_at' => '2021-01-18 23:24:44',
            ),
            85 => 
            array (
                'remote_id' => '509',
                'name' => 'SAC FEDERAL CREDIT UNION',
                'config' => '{"fid":"091901480","org":"SAC Federal CU","url":"https:\\/\\/pcu.sacfcu.com\\/scripts\\/isaofx.dll"}',
                'id' => 86,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:44',
                'updated_at' => '2021-01-18 23:24:44',
            ),
            86 => 
            array (
                'remote_id' => '510',
                'name' => 'Merrill Lynch&Co., Inc.',
                'config' => '{"fid":"5550","org":"Merrill Lynch & Co., Inc.","url":"https:\\/\\/taxcert.mlol.ml.com\\/eftxweb\\/access.ofx"}',
                'id' => 87,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:44',
                'updated_at' => '2021-01-18 23:24:44',
            ),
            87 => 
            array (
                'remote_id' => '511',
                'name' => 'Southeastern CU',
                'config' => '{"fid":"261271500","org":"Southeastern FCU","url":"https:\\/\\/moo.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 88,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:44',
                'updated_at' => '2021-01-18 23:24:44',
            ),
            88 => 
            array (
                'remote_id' => '512',
                'name' => 'Texas Dow Employees Credit Union',
                'config' => '{"fid":"313185515","org":"TexasDow","url":"https:\\/\\/allthetime.tdecu.org\\/scripts\\/isaofx.dll"}',
                'id' => 89,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:44',
                'updated_at' => '2021-01-18 23:24:44',
            ),
            89 => 
            array (
                'remote_id' => '513',
                'name' => 'University Federal Credit Union',
                'config' => '{"fid":"314977405","org":"Univerisity FCU","url":"https:\\/\\/OnDemand.ufcu.org\\/scripts\\/isaofx.dll"}',
                'id' => 90,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:44',
                'updated_at' => '2021-01-18 23:24:44',
            ),
            90 => 
            array (
                'remote_id' => '514',
                'name' => 'Yakima Valley Credit Union',
                'config' => '{"fid":"325183796","org":"Yakima Valley Credit Union","url":"https:\\/\\/secure1.yvcu.org\\/scripts\\/isaofx.dll"}',
                'id' => 91,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:45',
                'updated_at' => '2021-01-18 23:24:45',
            ),
            91 => 
            array (
                'remote_id' => '515',
                'name' => 'First Community FCU',
                'config' => '{"fid":"272483633","org":"FirstCommunityFCU","url":"https:\\/\\/pcu.1stcomm.org\\/scripts\\/isaofx.dll"}',
                'id' => 92,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:45',
                'updated_at' => '2021-01-18 23:24:45',
            ),
            92 => 
            array (
                'remote_id' => '516',
                'name' => 'Wells Fargo Advisor',
                'config' => '{"fid":"1030","org":"strong.com","url":"https:\\/\\/ofx.wellsfargoadvantagefunds.com\\/eftxWeb\\/Access.ofx"}',
                'id' => 93,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:45',
                'updated_at' => '2021-01-18 23:24:45',
            ),
            93 => 
            array (
                'remote_id' => '517',
                'name' => 'Chicago Patrolmens FCU',
                'config' => '{"fid":"271078146","org":"Chicago Patrolmens CU","url":"https:\\/\\/chp.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 94,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:45',
                'updated_at' => '2021-01-18 23:24:45',
            ),
            94 => 
            array (
                'remote_id' => '518',
                'name' => 'Signal Financial Federal Credit Union',
                'config' => '{"fid":"255075495","org":"Washington Telephone FCU","url":"https:\\/\\/webpb.sfonline.org\\/scripts\\/isaofx.dll"}',
                'id' => 95,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:45',
                'updated_at' => '2021-01-18 23:24:45',
            ),
            95 => 
            array (
                'remote_id' => '520',
                'name' => 'Bank-Fund Staff FCU',
                'config' => '{"fid":"2","org":"Bank Fund Staff FCU","url":"https:\\/\\/secure.bfsfcu.org\\/ofx\\/ofx.dll"}',
                'id' => 96,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:45',
                'updated_at' => '2021-01-18 23:24:45',
            ),
            96 => 
            array (
                'remote_id' => '521',
                'name' => 'APCO EMPLOYEES CREDIT UNION',
                'config' => '{"fid":"262087609","org":"APCO Employees Credit Union","url":"https:\\/\\/apc.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 97,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:46',
                'updated_at' => '2021-01-18 23:24:46',
            ),
            97 => 
            array (
                'remote_id' => '522',
                'name' => 'Bank of Tampa, The',
                'config' => '{"fid":"063108680","org":"BOT","url":"https:\\/\\/OFXDownload.Bankoftampa.com\\/OFXServer\\/ofxsrvr.dll"}',
                'id' => 98,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:46',
                'updated_at' => '2021-01-18 23:24:46',
            ),
            98 => 
            array (
                'remote_id' => '523',
                'name' => 'Cedar Point Federal Credit Union',
                'config' => '{"fid":"255077736","org":"Cedar Point Federal Credit Union","url":"https:\\/\\/pcu.cpfcu.com\\/scripts\\/isaofx.dll"}',
                'id' => 99,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:46',
                'updated_at' => '2021-01-18 23:24:46',
            ),
            99 => 
            array (
                'remote_id' => '524',
                'name' => 'Las Colinas FCU',
                'config' => '{"fid":"311080573","org":"Las Colinas Federal CU","url":"https:\\/\\/las.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 100,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:46',
                'updated_at' => '2021-01-18 23:24:46',
            ),
            100 => 
            array (
                'remote_id' => '525',
                'name' => 'McCoy Federal Credit Union',
                'config' => '{"fid":"263179956","org":"McCoy Federal Credit Union","url":"https:\\/\\/www.mccoydirect.org\\/scripts\\/isaofx.dll"}',
                'id' => 101,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:46',
                'updated_at' => '2021-01-18 23:24:46',
            ),
            101 => 
            array (
                'remote_id' => '526',
                'name' => 'Old National Bank',
                'config' => '{"fid":"11638","org":"ONB","url":"https:\\/\\/www.ofx.oldnational.com\\/ofxpreprocess.asp"}',
                'id' => 102,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:46',
                'updated_at' => '2021-01-18 23:24:46',
            ),
            102 => 
            array (
                'remote_id' => '527',
                'name' => 'Citizens Bank - Consumer',
                'config' => '{"fid":"CTZBK","org":"CheckFree OFXDownload","url":"https:\\/\\/www.oasis.cfree.com\\/0CTZBK.ofxgp"}',
                'id' => 103,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:46',
                'updated_at' => '2021-01-18 23:24:46',
            ),
            103 => 
            array (
                'remote_id' => '528',
                'name' => 'Citizens Bank - Business',
                'config' => '{"fid":"4639","org":"CheckFree OFXDownload","url":"https:\\/\\/www.oasis.cfree.com\\/04639.ofxgp"}',
                'id' => 104,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:46',
                'updated_at' => '2021-01-18 23:24:46',
            ),
            104 => 
            array (
                'remote_id' => '529',
                'name' => 'Century Federal Credit Union',
                'config' => '{"fid":"241075056","org":"CenturyFederalCU","url":"https:\\/\\/pcu.cenfedcu.org\\/scripts\\/isaofx.dll"}',
                'id' => 105,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:47',
                'updated_at' => '2021-01-18 23:24:47',
            ),
            105 => 
            array (
                'remote_id' => '530',
                'name' => 'ABNB Federal Credit Union',
                'config' => '{"fid":"251481627","org":"ABNB Federal Credit Union","url":"https:\\/\\/cuathome.abnbfcu.org\\/scripts\\/isaofx.dll"}',
                'id' => 106,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:47',
                'updated_at' => '2021-01-18 23:24:47',
            ),
            106 => 
            array (
                'remote_id' => '531',
                'name' => 'Allegiance Credit Union',
                'config' => '{"fid":"303085230","org":"Federal Employees CU","url":"https:\\/\\/fed.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 107,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:47',
                'updated_at' => '2021-01-18 23:24:47',
            ),
            107 => 
            array (
                'remote_id' => '532',
                'name' => 'Wright Patman Congressional FCU',
                'config' => '{"fid":"254074345","org":"Wright Patman Congressional FCU","url":"https:\\/\\/www.congressionalonline.org\\/scripts\\/isaofx.dll"}',
                'id' => 108,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:47',
                'updated_at' => '2021-01-18 23:24:47',
            ),
            108 => 
            array (
                'remote_id' => '533',
                'name' => 'America First Credit Union',
                'config' => '{"fid":"54324","org":"America First Credit Union","url":"https:\\/\\/ofx.americafirst.com"}',
                'id' => 109,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:47',
                'updated_at' => '2021-01-18 23:24:47',
            ),
            109 => 
            array (
                'remote_id' => '534',
                'name' => 'Motorola Employees Credit Union',
                'config' => '{"fid":"271984311","org":"Motorola Employees CU","url":"https:\\/\\/mecuofx.mecunet.org\\/scripts\\/isaofx.dll"}',
                'id' => 110,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:47',
                'updated_at' => '2021-01-18 23:24:47',
            ),
            110 => 
            array (
                'remote_id' => '535',
            'name' => 'Finance Center FCU (IN)',
                'config' => '{"fid":"274073876","org":"Finance Center FCU","url":"https:\\/\\/sec.fcfcu.com\\/scripts\\/isaofx.dll"}',
                'id' => 111,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:47',
                'updated_at' => '2021-01-18 23:24:47',
            ),
            111 => 
            array (
                'remote_id' => '536',
                'name' => 'Fort Knox Federal Credit Union',
                'config' => '{"fid":"283978425","org":"Fort Knox Federal Credit Union","url":"https:\\/\\/fcs1.fkfcu.org\\/scripts\\/isaofx.dll"}',
                'id' => 112,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:47',
                'updated_at' => '2021-01-18 23:24:47',
            ),
            112 => 
            array (
                'remote_id' => '537',
                'name' => 'Wachovia Bank',
                'config' => '{"fid":"4309","org":"Wachovia","url":"https:\\/\\/pfmpw.wachovia.com\\/cgi-forte\\/fortecgi?servicename=ofx&pagename=PFM"}',
                'id' => 113,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:47',
                'updated_at' => '2021-01-18 23:24:47',
            ),
            113 => 
            array (
                'remote_id' => '538',
                'name' => 'Think Federal Credit Union',
                'config' => '{"fid":"291975465","org":"IBMCU","url":"https:\\/\\/ofx.ibmcu.com"}',
                'id' => 114,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:47',
                'updated_at' => '2021-01-18 23:24:47',
            ),
            114 => 
            array (
                'remote_id' => '539',
                'name' => 'PSECU',
                'config' => '{"fid":"54354","org":"Teknowledge","url":"https:\\/\\/ofx.psecu.com\\/servlet\\/OFXServlet"}',
                'id' => 115,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:48',
                'updated_at' => '2021-01-18 23:24:48',
            ),
            115 => 
            array (
                'remote_id' => '540',
                'name' => 'Envision Credit Union',
                'config' => '{"fid":"263182558","org":"Envision Credit Union","url":"https:\\/\\/pcu.envisioncu.com\\/scripts\\/isaofx.dll"}',
                'id' => 116,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:48',
                'updated_at' => '2021-01-18 23:24:48',
            ),
            116 => 
            array (
                'remote_id' => '541',
                'name' => 'Columbia Credit Union',
                'config' => '{"fid":"323383349","org":"Columbia Credit Union","url":"https:\\/\\/ofx.columbiacu.org\\/scripts\\/isaofx.dll"}',
                'id' => 117,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:48',
                'updated_at' => '2021-01-18 23:24:48',
            ),
            117 => 
            array (
                'remote_id' => '542',
                'name' => '1st Advantage FCU',
                'config' => '{"fid":"251480563","org":"1st Advantage FCU","url":"https:\\/\\/members.1stadvantage.org\\/scripts\\/isaofx.dll"}',
                'id' => 118,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:48',
                'updated_at' => '2021-01-18 23:24:48',
            ),
            118 => 
            array (
                'remote_id' => '543',
                'name' => 'Central Maine FCU',
                'config' => '{"fid":"211287926","org":"Central Maine FCU","url":"https:\\/\\/cro.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 119,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:48',
                'updated_at' => '2021-01-18 23:24:48',
            ),
            119 => 
            array (
                'remote_id' => '544',
                'name' => 'Kirtland Federal Credit Union',
                'config' => '{"fid":"307070050","org":"Kirtland Federal Credit Union","url":"https:\\/\\/pcu.kirtlandfcu.org\\/scripts\\/isaofx.dll"}',
                'id' => 120,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:49',
                'updated_at' => '2021-01-18 23:24:49',
            ),
            120 => 
            array (
                'remote_id' => '545',
                'name' => 'Chesterfield Federal Credit Union',
                'config' => '{"fid":"251480327","org":"Chesterfield Employees FCU","url":"https:\\/\\/chf.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 121,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:49',
                'updated_at' => '2021-01-18 23:24:49',
            ),
            121 => 
            array (
                'remote_id' => '546',
                'name' => 'Campus USA Credit Union',
                'config' => '{"fid":"263178478","org":"Campus USA Credit Union","url":"https:\\/\\/que.campuscu.com\\/scripts\\/isaofx.dll"}',
                'id' => 122,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:49',
                'updated_at' => '2021-01-18 23:24:49',
            ),
            122 => 
            array (
                'remote_id' => '547',
            'name' => 'Summit Credit Union (WI)',
                'config' => '{"fid":"275979034","org":"Summit Credit Union","url":"https:\\/\\/branch.summitcreditunion.com\\/scripts\\/isaofx.dll"}',
                'id' => 123,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:49',
                'updated_at' => '2021-01-18 23:24:49',
            ),
            123 => 
            array (
                'remote_id' => '548',
                'name' => 'Financial Center CU',
                'config' => '{"fid":"321177803","org":"Fincancial Center Credit Union","url":"https:\\/\\/fin.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 124,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:49',
                'updated_at' => '2021-01-18 23:24:49',
            ),
            124 => 
            array (
                'remote_id' => '549',
                'name' => 'Hawaiian Tel Federal Credit Union',
                'config' => '{"fid":"321379070","org":"Hawaiian Tel FCU","url":"https:\\/\\/htl.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 125,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:49',
                'updated_at' => '2021-01-18 23:24:49',
            ),
            125 => 
            array (
                'remote_id' => '550',
                'name' => 'Addison Avenue Federal Credit Union',
                'config' => '{"fid":"11288","org":"hpcu","url":"https:\\/\\/ofx.addisonavenue.com"}',
                'id' => 126,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:49',
                'updated_at' => '2021-01-18 23:24:49',
            ),
            126 => 
            array (
                'remote_id' => '551',
                'name' => 'Navy Army Federal Credit Union',
                'config' => '{"fid":"111904503","org":"Navy Army Federal Credit Union","url":"https:\\/\\/mybranch.navyarmyfcu.com\\/scripts\\/isaofx.dll"}',
                'id' => 127,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:49',
                'updated_at' => '2021-01-18 23:24:49',
            ),
            127 => 
            array (
                'remote_id' => '552',
                'name' => 'Nevada Federal Credit Union',
                'config' => '{"fid":"10888","org":"PSI","url":"https:\\/\\/ssl4.nevadafederal.org\\/ofxdirect\\/ofxrqst.aspx"}',
                'id' => 128,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:50',
                'updated_at' => '2021-01-18 23:24:50',
            ),
            128 => 
            array (
                'remote_id' => '553',
                'name' => '66 Federal Credit Union',
                'config' => '{"fid":"289","org":"SixySix","url":"https:\\/\\/ofx.cuonlineaccounts.org"}',
                'id' => 129,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:50',
                'updated_at' => '2021-01-18 23:24:50',
            ),
            129 => 
            array (
                'remote_id' => '554',
                'name' => 'FirstBank of Colorado',
                'config' => '{"fid":"FirstBank","org":"FBDC","url":"https:\\/\\/www.efirstbankpfm.com\\/ofx\\/OFXServlet"}',
                'id' => 130,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:50',
                'updated_at' => '2021-01-18 23:24:50',
            ),
            130 => 
            array (
                'remote_id' => '555',
                'name' => 'Continental Federal Credit Union',
                'config' => '{"fid":"322077559","org":"Continenetal FCU","url":"https:\\/\\/cnt.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 131,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:50',
                'updated_at' => '2021-01-18 23:24:50',
            ),
            131 => 
            array (
                'remote_id' => '556',
                'name' => 'Fremont Bank',
                'config' => '{"fid":"121107882","org":"Fremont Bank","url":"https:\\/\\/ofx.fremontbank.com\\/OFXServer\\/FBOFXSrvr.dll"}',
                'id' => 132,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:50',
                'updated_at' => '2021-01-18 23:24:50',
            ),
            132 => 
            array (
                'remote_id' => '557',
                'name' => 'Peninsula Community Federal Credit Union',
                'config' => '{"fid":"325182344","org":"Peninsula Credit Union","url":"https:\\/\\/mas.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 133,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:50',
                'updated_at' => '2021-01-18 23:24:50',
            ),
            133 => 
            array (
                'remote_id' => '558',
                'name' => 'Fidelity NetBenefits',
                'config' => '{"fid":"8288","org":"nbofx.fidelity.com","url":"https:\\/\\/nbofx.fidelity.com\\/netbenefits\\/ofx\\/download"}',
                'id' => 134,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:51',
                'updated_at' => '2021-01-18 23:24:51',
            ),
            134 => 
            array (
                'remote_id' => '559',
                'name' => 'Fall River Municipal CU',
                'config' => '{"fid":"211382591","org":"Fall River Municipal CU","url":"https:\\/\\/fal.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 135,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:51',
                'updated_at' => '2021-01-18 23:24:51',
            ),
            135 => 
            array (
                'remote_id' => '560',
                'name' => 'University Credit Union',
                'config' => '{"fid":"267077850","org":"University Credit Union","url":"https:\\/\\/umc.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 136,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:52',
                'updated_at' => '2021-01-18 23:24:52',
            ),
            136 => 
            array (
                'remote_id' => '561',
                'name' => 'Dominion Credit Union',
                'config' => '{"fid":"251082644","org":"Dominion Credit Union","url":"https:\\/\\/dom.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 137,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:53',
                'updated_at' => '2021-01-18 23:24:53',
            ),
            137 => 
            array (
                'remote_id' => '562',
                'name' => 'HFS Federal Credit Union',
                'config' => '{"fid":"321378660","org":"HFS Federal Credit Union","url":"https:\\/\\/hfs.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 138,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:53',
                'updated_at' => '2021-01-18 23:24:53',
            ),
            138 => 
            array (
                'remote_id' => '563',
                'name' => 'IronStone Bank',
                'config' => '{"fid":"5012","org":"Atlantic States Bank","url":"https:\\/\\/www.oasis.cfree.com\\/5012.ofxgp"}',
                'id' => 139,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:53',
                'updated_at' => '2021-01-18 23:24:53',
            ),
            139 => 
            array (
                'remote_id' => '564',
                'name' => 'Utah Community Credit Union',
                'config' => '{"fid":"324377820","org":"Utah Community Credit Union","url":"https:\\/\\/ofx.uccu.com\\/scripts\\/isaofx.dll"}',
                'id' => 140,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:54',
                'updated_at' => '2021-01-18 23:24:54',
            ),
            140 => 
            array (
                'remote_id' => '565',
                'name' => 'OptionsXpress, Inc',
                'config' => '{"fid":"10876","org":"10876","url":"https:\\/\\/ofx.optionsxpress.com\\/cgi-bin\\/ox.exe"}',
                'id' => 141,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:54',
                'updated_at' => '2021-01-18 23:24:54',
            ),
            141 => 
            array (
                'remote_id' => '567',
                'name' => 'Prudential Retirement',
                'config' => '{"fid":"1271","org":"Prudential Retirement Services","url":"https:\\/\\/ofx.prudential.com\\/eftxweb\\/EFTXWebRedirector"}',
                'id' => 142,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:54',
                'updated_at' => '2021-01-18 23:24:54',
            ),
            142 => 
            array (
                'remote_id' => '568',
                'name' => 'Wells Fargo Investments, LLC',
                'config' => '{"fid":"10762","org":"wellsfargo.com","url":"https:\\/\\/invmnt.wellsfargo.com\\/inv\\/directConnect"}',
                'id' => 143,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:54',
                'updated_at' => '2021-01-18 23:24:54',
            ),
            143 => 
            array (
                'remote_id' => '570',
                'name' => 'Penson Financial Services',
                'config' => '{"fid":"10780","org":"Penson Financial Services Inc","url":"https:\\/\\/ofx.penson.com"}',
                'id' => 144,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:54',
                'updated_at' => '2021-01-18 23:24:54',
            ),
            144 => 
            array (
                'remote_id' => '571',
                'name' => 'Tri Boro Federal Credit Union',
                'config' => '{"fid":"243382747","org":"Tri Boro Federal Credit Union","url":"https:\\/\\/tri.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 145,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:54',
                'updated_at' => '2021-01-18 23:24:54',
            ),
            145 => 
            array (
                'remote_id' => '572',
                'name' => 'Hewitt Associates LLC',
                'config' => '{"fid":"242","org":"hewitt.com","url":"https:\\/\\/seven.was.hewitt.com\\/eftxweb\\/access.ofx"}',
                'id' => 146,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:54',
                'updated_at' => '2021-01-18 23:24:54',
            ),
            146 => 
            array (
                'remote_id' => '573',
                'name' => 'Delta Community Credit Union',
                'config' => '{"fid":"3328","org":"decu.org","url":"https:\\/\\/appweb.deltacommunitycu.com\\/ofxroot\\/directtocore.asp"}',
                'id' => 147,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:55',
                'updated_at' => '2021-01-18 23:24:55',
            ),
            147 => 
            array (
                'remote_id' => '574',
                'name' => 'Huntington National Bank',
                'config' => '{"fid":"3701","org":"Huntington","url":"https:\\/\\/onlinebanking.huntington.com\\/scripts\\/serverext.dll"}',
                'id' => 148,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:55',
                'updated_at' => '2021-01-18 23:24:55',
            ),
            148 => 
            array (
                'remote_id' => '575',
                'name' => 'WSECU',
                'config' => '{"fid":"325181028","org":"WSECU","url":"https:\\/\\/ssl3.wsecu.org\\/ofxserver\\/ofxsrvr.dll"}',
                'id' => 149,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:55',
                'updated_at' => '2021-01-18 23:24:55',
            ),
            149 => 
            array (
                'remote_id' => '576',
                'name' => 'Baton Rouge City Parish Emp FCU',
                'config' => '{"fid":"265473333","org":"Baton Rouge City Parish EFCU","url":"https:\\/\\/bat.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 150,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:55',
                'updated_at' => '2021-01-18 23:24:55',
            ),
            150 => 
            array (
                'remote_id' => '577',
                'name' => 'Schools Financial Credit Union',
                'config' => '{"fid":"90001","org":"Teknowledge","url":"https:\\/\\/ofx.schools.org\\/TekPortalOFX\\/servlet\\/TP_OFX_Controller"}',
                'id' => 151,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:55',
                'updated_at' => '2021-01-18 23:24:55',
            ),
            151 => 
            array (
                'remote_id' => '578',
                'name' => 'Charles Schwab Bank, N.A.',
                'config' => '{"fid":"101","org":"ISC","url":"https:\\/\\/ofx.schwab.com\\/bankcgi_dev\\/ofx_server"}',
                'id' => 152,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:55',
                'updated_at' => '2021-01-18 23:24:55',
            ),
            152 => 
            array (
                'remote_id' => '579',
                'name' => 'NW Preferred Federal Credit Union',
                'config' => '{"fid":"323076575","org":"NW Preferred FCU","url":"https:\\/\\/nwf.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 153,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:55',
                'updated_at' => '2021-01-18 23:24:55',
            ),
            153 => 
            array (
                'remote_id' => '580',
                'name' => 'Camino FCU',
                'config' => '{"fid":"322279975","org":"Camino FCU","url":"https:\\/\\/homebanking.caminofcu.org\\/isaofx\\/isaofx.dll"}',
                'id' => 154,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:55',
                'updated_at' => '2021-01-18 23:24:55',
            ),
            154 => 
            array (
                'remote_id' => '581',
                'name' => 'Novartis Federal Credit Union',
                'config' => '{"fid":"221278556","org":"Novartis FCU","url":"https:\\/\\/cib.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 155,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:55',
                'updated_at' => '2021-01-18 23:24:55',
            ),
            155 => 
            array (
                'remote_id' => '582',
                'name' => 'U.S. First FCU',
                'config' => '{"fid":"321076289","org":"US First FCU","url":"https:\\/\\/uff.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 156,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:55',
                'updated_at' => '2021-01-18 23:24:55',
            ),
            156 => 
            array (
                'remote_id' => '583',
                'name' => 'FAA Technical Center FCU',
                'config' => '{"fid":"231277440","org":"FAA Technical Center FCU","url":"https:\\/\\/ftc.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 157,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:56',
                'updated_at' => '2021-01-18 23:24:56',
            ),
            157 => 
            array (
                'remote_id' => '584',
                'name' => 'Municipal Employees Credit Union of Baltimore, Inc.',
                'config' => '{"fid":"252076468","org":"Municipal ECU of Baltimore,Inc.","url":"https:\\/\\/mec.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 158,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:56',
                'updated_at' => '2021-01-18 23:24:56',
            ),
            158 => 
            array (
                'remote_id' => '585',
                'name' => 'Day Air Credit Union',
                'config' => '{"fid":"242277808","org":"Day Air Credit Union","url":"https:\\/\\/pcu.dayair.org\\/scripts\\/isaofx.dll"}',
                'id' => 159,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:56',
                'updated_at' => '2021-01-18 23:24:56',
            ),
            159 => 
            array (
                'remote_id' => '586',
                'name' => 'Texas State Bank - McAllen',
                'config' => '{"fid":"114909013","org":"Texas State Bank","url":"https:\\/\\/www.tsb-a.com\\/OFXServer\\/ofxsrvr.dll"}',
                'id' => 160,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:56',
                'updated_at' => '2021-01-18 23:24:56',
            ),
            160 => 
            array (
                'remote_id' => '587',
                'name' => 'OCTFCU',
                'config' => '{"fid":"17600","org":"OCTFCU","url":"https:\\/\\/ofx.octfcu.org"}',
                'id' => 161,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:56',
                'updated_at' => '2021-01-18 23:24:56',
            ),
            161 => 
            array (
                'remote_id' => '588',
                'name' => 'Hawaii State FCU',
                'config' => '{"fid":"321379041","org":"Hawaii State FCU","url":"https:\\/\\/hse.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 162,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:56',
                'updated_at' => '2021-01-18 23:24:56',
            ),
            162 => 
            array (
                'remote_id' => '592',
                'name' => 'Community First Credit Union',
                'config' => '{"fid":"275982801","org":"Community First Credit Union","url":"https:\\/\\/pcu.communityfirstcu.org\\/scripts\\/isaofx.dll"}',
                'id' => 163,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:56',
                'updated_at' => '2021-01-18 23:24:56',
            ),
            163 => 
            array (
                'remote_id' => '593',
                'name' => 'MTC Federal Credit Union',
                'config' => '{"fid":"053285173","org":"MTC Federal Credit Union","url":"https:\\/\\/mic.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 164,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:56',
                'updated_at' => '2021-01-18 23:24:56',
            ),
            164 => 
            array (
                'remote_id' => '594',
            'name' => 'Home Federal Savings Bank(MN/IA)',
                'config' => '{"fid":"291270050","org":"VOneTwentySevenG","url":"https:\\/\\/ofx1.evault.ws\\/ofxserver\\/ofxsrvr.dll"}',
                'id' => 165,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:56',
                'updated_at' => '2021-01-18 23:24:56',
            ),
            165 => 
            array (
                'remote_id' => '595',
                'name' => 'Reliant Community Credit Union',
                'config' => '{"fid":"222382438","org":"W.C.T.A Federal Credit Union","url":"https:\\/\\/wct.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 166,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:56',
                'updated_at' => '2021-01-18 23:24:56',
            ),
            166 => 
            array (
                'remote_id' => '596',
                'name' => 'Patriots Federal Credit Union',
                'config' => '{"fid":"322281963","org":"PAT FCU","url":"https:\\/\\/pat.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 167,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:57',
                'updated_at' => '2021-01-18 23:24:57',
            ),
            167 => 
            array (
                'remote_id' => '597',
                'name' => 'SafeAmerica Credit Union',
                'config' => '{"fid":"321171757","org":"SafeAmerica Credit Union","url":"https:\\/\\/saf.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 168,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:57',
                'updated_at' => '2021-01-18 23:24:57',
            ),
            168 => 
            array (
                'remote_id' => '598',
                'name' => 'Mayo Employees Federal Credit Union',
                'config' => '{"fid":"291975478","org":"Mayo Employees FCU","url":"https:\\/\\/homebank.mayocreditunion.org\\/ofx\\/ofx.dll"}',
                'id' => 169,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:57',
                'updated_at' => '2021-01-18 23:24:57',
            ),
            169 => 
            array (
                'remote_id' => '599',
                'name' => 'FivePoint Credit Union',
                'config' => '{"fid":"313187571","org":"FivePoint Credit Union","url":"https:\\/\\/tfcu-nfuse01.texacocommunity.org\\/internetconnector\\/isaofx.dll"}',
                'id' => 170,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:57',
                'updated_at' => '2021-01-18 23:24:57',
            ),
            170 => 
            array (
                'remote_id' => '600',
                'name' => 'Community Resource Bank',
                'config' => '{"fid":"091917160","org":"CNB","url":"https:\\/\\/www.cnbinternet.com\\/OFXServer\\/ofxsrvr.dll"}',
                'id' => 171,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:57',
                'updated_at' => '2021-01-18 23:24:57',
            ),
            171 => 
            array (
                'remote_id' => '601',
                'name' => 'Security 1st FCU',
                'config' => '{"fid":"314986292","org":"Security 1st FCU","url":"https:\\/\\/sec.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 172,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:57',
                'updated_at' => '2021-01-18 23:24:57',
            ),
            172 => 
            array (
                'remote_id' => '602',
                'name' => 'First Alliance Credit Union',
                'config' => '{"fid":"291975481","org":"First Alliance Credit Union","url":"https:\\/\\/fia.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 173,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:57',
                'updated_at' => '2021-01-18 23:24:57',
            ),
            173 => 
            array (
                'remote_id' => '603',
                'name' => 'Billings Federal Credit Union',
                'config' => '{"fid":"6217","org":"Billings Federal Credit Union","url":"https:\\/\\/bfcuonline.billingsfcu.org\\/ofx\\/ofx.dll"}',
                'id' => 174,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:57',
                'updated_at' => '2021-01-18 23:24:57',
            ),
            174 => 
            array (
                'remote_id' => '604',
                'name' => 'Windward Community FCU',
                'config' => '{"fid":"321380315","org":"Windward Community FCU","url":"https:\\/\\/wwc.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 175,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:58',
                'updated_at' => '2021-01-18 23:24:58',
            ),
            175 => 
            array (
                'remote_id' => '606',
                'name' => 'Siouxland Federal Credit Union',
                'config' => '{"fid":"304982235","org":"SIOUXLAND FCU","url":"https:\\/\\/sio.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 176,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:58',
                'updated_at' => '2021-01-18 23:24:58',
            ),
            176 => 
            array (
                'remote_id' => '607',
                'name' => 'The Queen\'s Federal Credit Union',
                'config' => '{"fid":"321379504","org":"The Queens Federal Credit Union","url":"https:\\/\\/que.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 177,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:58',
                'updated_at' => '2021-01-18 23:24:58',
            ),
            177 => 
            array (
                'remote_id' => '608',
                'name' => 'Edward Jones',
                'config' => '{"fid":"823","org":"Edward Jones","url":"https:\\/\\/ofx.edwardjones.com"}',
                'id' => 178,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:58',
                'updated_at' => '2021-01-18 23:24:58',
            ),
            178 => 
            array (
                'remote_id' => '609',
                'name' => 'Merck Sharp&Dohme FCU',
                'config' => '{"fid":"231386645","org":"MERCK, SHARPE&DOHME FCU","url":"https:\\/\\/msd.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 179,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:58',
                'updated_at' => '2021-01-18 23:24:58',
            ),
            179 => 
            array (
                'remote_id' => '610',
                'name' => 'Credit Union 1 - IL',
                'config' => '{"fid":"271188081","org":"Credit Union 1","url":"https:\\/\\/pcu.creditunion1.org\\/scripts\\/isaofx.dll"}',
                'id' => 180,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:59',
                'updated_at' => '2021-01-18 23:24:59',
            ),
            180 => 
            array (
                'remote_id' => '611',
                'name' => 'Bossier Federal Credit Union',
                'config' => '{"fid":"311175129","org":"Bossier Federal Credit Union","url":"https:\\/\\/bos.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 181,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:59',
                'updated_at' => '2021-01-18 23:24:59',
            ),
            181 => 
            array (
                'remote_id' => '612',
                'name' => 'First Florida Credit Union',
                'config' => '{"fid":"263079014","org":"First Llorida Credit Union","url":"https:\\/\\/pcu2.gecuf.org\\/scripts\\/isaofx.dll"}',
                'id' => 182,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:59',
                'updated_at' => '2021-01-18 23:24:59',
            ),
            182 => 
            array (
                'remote_id' => '613',
                'name' => 'NorthEast Alliance FCU',
                'config' => '{"fid":"221982130","org":"NorthEast Alliance FCU","url":"https:\\/\\/nea.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 183,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:59',
                'updated_at' => '2021-01-18 23:24:59',
            ),
            183 => 
            array (
                'remote_id' => '614',
                'name' => 'ShareBuilder',
                'config' => '{"fid":"5575","org":"ShareBuilder","url":"https:\\/\\/ofx.sharebuilder.com"}',
                'id' => 184,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:59',
                'updated_at' => '2021-01-18 23:24:59',
            ),
            184 => 
            array (
                'remote_id' => '616',
                'name' => 'Weitz Funds',
                'config' => '{"fid":"weitz.com","org":"weitz.com","url":"https:\\/\\/www3.financialtrans.com\\/tf\\/OFXServer?tx=OFXController&cz=702110804131918&cl=52204081925"}',
                'id' => 185,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:24:59',
                'updated_at' => '2021-01-18 23:24:59',
            ),
            185 => 
            array (
                'remote_id' => '617',
                'name' => 'JPMorgan Retirement Plan Services',
                'config' => '{"fid":"6313","org":"JPMORGAN","url":"https:\\/\\/ofx.retireonline.com\\/eftxweb\\/access.ofx"}',
                'id' => 186,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:00',
                'updated_at' => '2021-01-18 23:25:00',
            ),
            186 => 
            array (
                'remote_id' => '618',
                'name' => 'Credit Union ONE',
                'config' => '{"fid":"14412","org":"Credit Union ONE","url":"https:\\/\\/cuhome.cuone.org\\/ofx\\/ofx.dll"}',
                'id' => 187,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:00',
                'updated_at' => '2021-01-18 23:25:00',
            ),
            187 => 
            array (
                'remote_id' => '619',
                'name' => 'Salt Lake City Credit Union',
                'config' => '{"fid":"324079186","org":"Salt Lake City Credit Union","url":"https:\\/\\/slc.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                'id' => 188,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:00',
                'updated_at' => '2021-01-18 23:25:00',
            ),
            188 => 
            array (
                'remote_id' => '620',
                'name' => 'First Southwest Company',
                'config' => '{"fid":"7048","org":"AFS","url":"https:\\/\\/fswofx.automatedfinancial.com"}',
                'id' => 189,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:00',
                'updated_at' => '2021-01-18 23:25:00',
            ),
            189 => 
            array (
                'remote_id' => '622',
                'name' => 'Wells Fargo Trust-Investment Mgt',
                'config' => '{"fid":"6955","org":"Wells Fargo Trust","url":"https:\\/\\/trust.wellsfargo.com\\/trust\\/directConnect"}',
                'id' => 190,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:00',
                'updated_at' => '2021-01-18 23:25:00',
            ),
            190 => 
            array (
                'remote_id' => '623',
                'name' => 'Scottrade, Inc.',
                'config' => '{"fid":"777","org":"Scottrade","url":"https:\\/\\/ofxstl.scottsave.com"}',
                'id' => 191,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:00',
                'updated_at' => '2021-01-18 23:25:00',
            ),
            191 => 
            array (
                'remote_id' => '624',
                'name' => 'Silver State Schools CU',
                'config' => '{"fid":"322484265","org":"SSSCU","url":"https:\\/\\/www.silverstatecu.com\\/OFXServer\\/ofxsrvr.dll"}',
                'id' => 192,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:00',
                'updated_at' => '2021-01-18 23:25:00',
            ),
            192 => 
            array (
                'remote_id' => '626',
                'name' => 'VISA Information Source',
                'config' => '{"fid":"10942","org":"VISA","url":"https:\\/\\/vis.informationmanagement.visa.com\\/eftxweb\\/access.ofx"}',
                'id' => 193,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:01',
                'updated_at' => '2021-01-18 23:25:01',
            ),
            193 => 
            array (
                'remote_id' => '627',
                'name' => 'National City',
                'config' => '{"fid":"5860","org":"NATIONAL CITY","url":"https:\\/\\/ofx.nationalcity.com\\/ofx\\/OFXConsumer.aspx"}',
                'id' => 194,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:01',
                'updated_at' => '2021-01-18 23:25:01',
            ),
            194 => 
            array (
                'remote_id' => '628',
                'name' => 'Capital One',
                'config' => '{"fid":"1001","org":"Hibernia","url":"https:\\/\\/onlinebanking.capitalone.com\\/scripts\\/serverext.dll"}',
                'id' => 195,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:01',
                'updated_at' => '2021-01-18 23:25:01',
            ),
            195 => 
            array (
                'remote_id' => '629',
                'name' => 'Citi Credit Card',
                'config' => '{"fid":"24909","org":"Citigroup","url":"https:\\/\\/www.accountonline.com\\/cards\\/svc\\/CitiOfxManager.do"}',
                'id' => 196,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:01',
                'updated_at' => '2021-01-18 23:25:01',
            ),
            196 => 
            array (
                'remote_id' => '630',
                'name' => 'Zions Bank',
                'config' => '{"fid":"1115","org":"244-3","url":"https:\\/\\/quicken.metavante.com\\/ofx\\/OFXServlet"}',
                'id' => 197,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:01',
                'updated_at' => '2021-01-18 23:25:01',
            ),
            197 => 
            array (
                'remote_id' => '631',
                'name' => 'Capital One Bank',
                'config' => '{"fid":"1001","org":"Hibernia","url":"https:\\/\\/onlinebanking.capitalone.com\\/scripts\\/serverext.dll"}',
                'id' => 198,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:01',
                'updated_at' => '2021-01-18 23:25:01',
            ),
            198 => 
            array (
                'remote_id' => '633',
                'name' => 'Redstone Federal Credit Union',
                'config' => '{"fid":"2143","org":"Harland Financial Solutions","url":"https:\\/\\/remotebanking.redfcu.org\\/ofx\\/ofx.dll"}',
                'id' => 199,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:02',
                'updated_at' => '2021-01-18 23:25:02',
            ),
            199 => 
            array (
                'remote_id' => '634',
                'name' => 'PNC Bank',
                'config' => '{"fid":"4501","org":"ISC","url":"https:\\/\\/www.oasis.cfree.com\\/fip\\/genesis\\/prod\\/04501.ofx"}',
                'id' => 200,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:02',
                'updated_at' => '2021-01-18 23:25:02',
            ),
            200 => 
            array (
                'remote_id' => '635',
            'name' => 'Bank of America (California)',
                'config' => '{"fid":"6805","org":"HAN","url":"https:\\/\\/ofx.bankofamerica.com\\/cgi-forte\\/ofx?servicename=ofx_2-3&pagename=bofa"}',
                'id' => 201,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:02',
                'updated_at' => '2021-01-18 23:25:02',
            ),
            201 => 
            array (
                'remote_id' => '636',
            'name' => 'Chase (credit card) ',
                'config' => '{"fid":"10898","org":"B1","url":"https:\\/\\/ofx.chase.com"}',
                'id' => 202,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:02',
                'updated_at' => '2021-01-18 23:25:02',
            ),
            202 => 
            array (
                'remote_id' => '637',
                'name' => 'Arizona Federal Credit Union',
                'config' => '{"fid":"322172797","org":"DI","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                'id' => 203,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:02',
                'updated_at' => '2021-01-18 23:25:02',
            ),
            203 => 
            array (
                'remote_id' => '638',
                'name' => 'UW Credit Union',
                'config' => '{"fid":"1001","org":"UWCU","url":"https:\\/\\/ofx.uwcu.org\\/serverext.dll"}',
                'id' => 204,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:02',
                'updated_at' => '2021-01-18 23:25:02',
            ),
            204 => 
            array (
                'remote_id' => '639',
                'name' => 'Bank of America',
                'config' => '{"fid":"5959","org":"HAN","url":"https:\\/\\/eftx.bankofamerica.com\\/eftxweb\\/access.ofx"}',
                'id' => 205,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:02',
                'updated_at' => '2021-01-18 23:25:02',
            ),
            205 => 
            array (
                'remote_id' => '640',
                'name' => 'Commerce Bank',
                'config' => '{"fid":"1001","org":"CommerceBank","url":"https:\\/\\/ofx.tdbank.com\\/scripts\\/serverext.dll"}',
                'id' => 206,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:02',
                'updated_at' => '2021-01-18 23:25:02',
            ),
            206 => 
            array (
                'remote_id' => '641',
                'name' => 'Securities America',
                'config' => '{"fid":"7784","org":"Fidelity","url":"https:\\/\\/ofx.ibgstreetscape.com:443"}',
                'id' => 207,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:03',
                'updated_at' => '2021-01-18 23:25:03',
            ),
            207 => 
            array (
                'remote_id' => '642',
                'name' => 'First Internet Bank of Indiana',
                'config' => '{"fid":"074014187","org":"DI","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                'id' => 208,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:03',
                'updated_at' => '2021-01-18 23:25:03',
            ),
            208 => 
            array (
                'remote_id' => '643',
                'name' => 'Alpine Banks of Colorado',
                'config' => '{"fid":"1451","org":"JackHenry","url":"https:\\/\\/directline.netteller.com"}',
                'id' => 209,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:03',
                'updated_at' => '2021-01-18 23:25:03',
            ),
            209 => 
            array (
                'remote_id' => '644',
                'name' => 'BancFirst',
                'config' => '{"fid":"103003632","org":"DI","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                'id' => 210,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:03',
                'updated_at' => '2021-01-18 23:25:03',
            ),
            210 => 
            array (
                'remote_id' => '645',
                'name' => 'Desert Schools Federal Credit Union',
                'config' => '{"fid":"1001","org":"DSFCU","url":"https:\\/\\/epal.desertschools.org\\/scripts\\/serverext.dll"}',
                'id' => 211,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:03',
                'updated_at' => '2021-01-18 23:25:03',
            ),
            211 => 
            array (
                'remote_id' => '646',
                'name' => 'Kinecta Federal Credit Union',
                'config' => '{"fid":"322278073","org":"KINECTA","url":"https:\\/\\/ofx.kinecta.org\\/OFXServer\\/ofxsrvr.dll"}',
                'id' => 212,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:04',
                'updated_at' => '2021-01-18 23:25:04',
            ),
            212 => 
            array (
                'remote_id' => '325081403',
                'name' => 'Boeing Employees Credit Union',
                'config' => '{"fid":"3670","org":"BECU","url":"https:\\/\\/onlinebanking.becu.org\\/ofx\\/ofxprocessor.asp"}',
                'id' => 213,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:04',
                'updated_at' => '2021-01-18 23:25:04',
            ),
            213 => 
            array (
                'remote_id' => '648',
                'name' => 'Capital One Bank - 2',
                'config' => '{"fid":"1001","org":"Hibernia","url":"https:\\/\\/onlinebanking.capitalone.com\\/ofx\\/process.ofx"}',
                'id' => 214,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:04',
                'updated_at' => '2021-01-18 23:25:04',
            ),
            214 => 
            array (
                'remote_id' => '649',
                'name' => 'Michigan State University Federal CU',
                'config' => '{"fid":"272479663","org":"MSUFCU","url":"https:\\/\\/ofx.msufcu.org\\/ofxserver\\/ofxsrvr.dll"}',
                'id' => 215,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:04',
                'updated_at' => '2021-01-18 23:25:04',
            ),
            215 => 
            array (
                'remote_id' => '650',
                'name' => 'The Community Bank',
                'config' => '{"fid":"211371476","org":"DI","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                'id' => 216,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:04',
                'updated_at' => '2021-01-18 23:25:04',
            ),
            216 => 
            array (
                'remote_id' => '651',
                'name' => 'Sacramento Credit Union',
                'config' => '{"fid":"1","org":"SACRAMENTO CREDIT UNION","url":"https:\\/\\/homebank.sactocu.org\\/ofx\\/ofx.dll"}',
                'id' => 217,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:04',
                'updated_at' => '2021-01-18 23:25:04',
            ),
            217 => 
            array (
                'remote_id' => '652',
                'name' => 'TD Bank',
                'config' => '{"fid":"1001","org":"CommerceBank","url":"https:\\/\\/onlinebanking.tdbank.com\\/scripts\\/serverext.dll"}',
                'id' => 218,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:05',
                'updated_at' => '2021-01-18 23:25:05',
            ),
            218 => 
            array (
                'remote_id' => '653',
                'name' => 'Suncoast Schools FCU',
                'config' => '{"fid":"1001","org":"SunCoast","url":"https:\\/\\/ofx.suncoastfcu.org"}',
                'id' => 219,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:05',
                'updated_at' => '2021-01-18 23:25:05',
            ),
            219 => 
            array (
                'remote_id' => '654',
                'name' => 'Metro Bank',
                'config' => '{"fid":"9970","org":"MTRO","url":"https:\\/\\/ofx.mymetrobank.com\\/ofx\\/ofx.ofx"}',
                'id' => 220,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:05',
                'updated_at' => '2021-01-18 23:25:05',
            ),
            220 => 
            array (
                'remote_id' => '655',
            'name' => 'First National Bank (Texas)',
                'config' => '{"fid":"12840","org":"JackHenry","url":"https:\\/\\/directline.netteller.com"}',
                'id' => 221,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:05',
                'updated_at' => '2021-01-18 23:25:05',
            ),
            221 => 
            array (
                'remote_id' => '656',
                'name' => 'Bank of the West',
                'config' => '{"fid":"5809","org":"BancWest Corp","url":"https:\\/\\/olbp.bankofthewest.com\\/ofx0002\\/ofx_isapi.dll"}',
                'id' => 222,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:05',
                'updated_at' => '2021-01-18 23:25:05',
            ),
            222 => 
            array (
                'remote_id' => '657',
                'name' => 'Mountain America Credit Union',
                'config' => '{"fid":"324079555","org":"MACU","url":"https:\\/\\/ofx.macu.org\\/OFXServer\\/ofxsrvr.dll"}',
                'id' => 223,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:06',
                'updated_at' => '2021-01-18 23:25:06',
            ),
            223 => 
            array (
                'remote_id' => '658',
                'name' => 'ING DIRECT',
                'config' => '{"fid":"031176110","org":"ING DIRECT","url":"https:\\/\\/ofx.ingdirect.com\\/OFXDownload\\/ofx.html"}',
                'id' => 224,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:06',
                'updated_at' => '2021-01-18 23:25:06',
            ),
            224 => 
            array (
                'remote_id' => '659',
                'name' => 'Santa Barbara Bank & Trust',
                'config' => '{"fid":"5524","org":"pfm-l3g","url":"https:\\/\\/pfm.metavante.com\\/ofx\\/OFXServlet"}',
                'id' => 225,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:06',
                'updated_at' => '2021-01-18 23:25:06',
            ),
            225 => 
            array (
                'remote_id' => '660',
                'name' => 'UMB',
                'config' => '{"fid":"468","org":"UMBOFX","url":"https:\\/\\/ofx.umb.com"}',
                'id' => 226,
                'bank_library_ifd' => 1,
                'created_at' => '2021-01-18 23:25:06',
                'updated_at' => '2021-01-18 23:25:06',
            ),
            226 => 
            array (
                'remote_id' => '661',
                'name' => 'Bank Of America(All except CA,WA,&ID ',
                    'config' => '{"fid":"6812","org":"HAN","url":"Https:\\/\\/ofx.bankofamerica.com\\/cgi-forte\\/fortecgi?servicename=ofx_2-3&pagename=ofx "}',
                    'id' => 227,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:06',
                    'updated_at' => '2021-01-18 23:25:06',
                ),
                227 => 
                array (
                    'remote_id' => '662',
                    'name' => 'Centra Credit Union2',
                    'config' => '{"fid":"274972883","org":"Centra CU","url":"https:\\/\\/www.centralink.org\\/scripts\\/isaofx.dll"}',
                    'id' => 228,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:06',
                    'updated_at' => '2021-01-18 23:25:06',
                ),
                228 => 
                array (
                    'remote_id' => '663',
                    'name' => 'Mainline National Bank',
                    'config' => '{"fid":"9869","org":"JackHenry","url":"https:\\/\\/directline.netteller.com"}',
                    'id' => 229,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:06',
                    'updated_at' => '2021-01-18 23:25:06',
                ),
                229 => 
                array (
                    'remote_id' => '664',
                    'name' => 'Citizens Bank',
                    'config' => '{"fid":"4639","org":"CheckFree OFXDownload","url":"https:\\/\\/www.oasis.cfree.com\\/04639.ofxgp"}',
                    'id' => 230,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:06',
                    'updated_at' => '2021-01-18 23:25:06',
                ),
                230 => 
                array (
                    'remote_id' => '665',
                    'name' => 'USAA Investment Mgmt Co',
                    'config' => '{"fid":"24592","org":"USAA","url":"https:\\/\\/service2.usaa.com\\/ofx\\/OFXServlet"}',
                    'id' => 231,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:07',
                    'updated_at' => '2021-01-18 23:25:07',
                ),
                231 => 
                array (
                    'remote_id' => '666',
                    'name' => '121 Financial Credit Union',
                    'config' => '{"fid":"000001155","org":"121 Financial Credit Union","url":"https:\\/\\/ppc.121fcu.org\\/scripts\\/isaofx.dll"}',
                    'id' => 232,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:07',
                    'updated_at' => '2021-01-18 23:25:07',
                ),
                232 => 
                array (
                    'remote_id' => '667',
                    'name' => 'Abbott Laboratories Employee CU',
                    'config' => '{"fid":"35MXN","org":"Abbott Laboratories ECU - ALEC","url":"https:\\/\\/www.netit.financial-net.com\\/ofx\\/"}',
                    'id' => 233,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:07',
                    'updated_at' => '2021-01-18 23:25:07',
                ),
                233 => 
                array (
                    'remote_id' => '668',
                    'name' => 'Achieva Credit Union',
                    'config' => '{"fid":"4491","org":"Achieva Credit Union","url":"https:\\/\\/rbserver.achievacu.com\\/ofx\\/ofx.dll"}',
                    'id' => 234,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:07',
                    'updated_at' => '2021-01-18 23:25:07',
                ),
                234 => 
                array (
                    'remote_id' => '669',
                    'name' => 'American National Bank',
                    'config' => '{"fid":"4201","org":"ISC","url":"https:\\/\\/www.oasis.cfree.com\\/fip\\/genesis\\/prod\\/04201.ofx"}',
                    'id' => 235,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:07',
                    'updated_at' => '2021-01-18 23:25:07',
                ),
                235 => 
                array (
                    'remote_id' => '670',
                    'name' => 'Andrews Federal Credit Union',
                    'config' => '{"fid":"AFCUSMD","org":"FundsXpress","url":"https:\\/\\/ofx.fundsxpress.com\\/piles\\/ofx.pile\\/"}',
                    'id' => 236,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:07',
                    'updated_at' => '2021-01-18 23:25:07',
                ),
                236 => 
                array (
                    'remote_id' => '671',
                    'name' => 'Citi Personal Wealth Management',
                    'config' => '{"fid":"060","org":"Citigroup","url":"https:\\/\\/uat-ofx.netxclient.inautix.com\\/cgi\\/OFXNetx"}',
                    'id' => 237,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:08',
                    'updated_at' => '2021-01-18 23:25:08',
                ),
                237 => 
                array (
                    'remote_id' => '672',
                'name' => 'Bank One (Chicago)',
                    'config' => '{"fid":"1501","org":"ISC","url":"https:\\/\\/www.oasis.cfree.com\\/fip\\/genesis\\/prod\\/01501.ofx"}',
                    'id' => 238,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:08',
                    'updated_at' => '2021-01-18 23:25:08',
                ),
                238 => 
                array (
                    'remote_id' => '673',
                'name' => 'Bank One (Michigan and Florida)',
                    'config' => '{"fid":"6001","org":"ISC","url":"https:\\/\\/www.oasis.cfree.com\\/fip\\/genesis\\/prod\\/06001.ofx"}',
                    'id' => 239,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:08',
                    'updated_at' => '2021-01-18 23:25:08',
                ),
                239 => 
                array (
                    'remote_id' => '674',
                'name' => 'Bank of America (Formerly Fleet)',
                    'config' => '{"fid":"1803","org":"ISC","url":"https:\\/\\/www.oasis.cfree.com\\/fip\\/genesis\\/prod\\/01803.ofx"}',
                    'id' => 240,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:08',
                    'updated_at' => '2021-01-18 23:25:08',
                ),
                240 => 
                array (
                    'remote_id' => '675',
                    'name' => 'BankBoston PC Banking',
                    'config' => '{"fid":"1801","org":"ISC","url":"https:\\/\\/www.oasis.cfree.com\\/fip\\/genesis\\/prod\\/01801.ofx"}',
                    'id' => 241,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:08',
                    'updated_at' => '2021-01-18 23:25:08',
                ),
                241 => 
                array (
                    'remote_id' => '676',
                    'name' => 'Beverly Co-Operative Bank',
                    'config' => '{"fid":"531","org":"orcc","url":"https:\\/\\/www19.onlinebank.com\\/OROFX16Listener"}',
                    'id' => 242,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:08',
                    'updated_at' => '2021-01-18 23:25:08',
                ),
                242 => 
                array (
                    'remote_id' => '677',
                    'name' => 'Cambridge Portuguese Credit Union',
                    'config' => '{"fid":"983","org":"orcc","url":"https:\\/\\/www20.onlinebank.com\\/OROFX16Listener"}',
                    'id' => 243,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:08',
                    'updated_at' => '2021-01-18 23:25:08',
                ),
                243 => 
                array (
                    'remote_id' => '678',
                    'name' => 'Citibank',
                    'config' => '{"fid":"2101","org":"ISC","url":"https:\\/\\/www.oasis.cfree.com\\/fip\\/genesis\\/prod\\/02101.ofx"}',
                    'id' => 244,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:09',
                    'updated_at' => '2021-01-18 23:25:09',
                ),
                244 => 
                array (
                    'remote_id' => '679',
                    'name' => 'Community Bank, N.A.',
                    'config' => '{"fid":"11517","org":"JackHenry","url":"https:\\/\\/directline2.netteller.com"}',
                    'id' => 245,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:09',
                    'updated_at' => '2021-01-18 23:25:09',
                ),
                245 => 
                array (
                    'remote_id' => '680',
                    'name' => 'Consumers Credit Union',
                    'config' => '{"fid":"12541","org":"Consumers Credit Union","url":"https:\\/\\/ofx.lanxtra.com\\/ofx\\/servlet\\/Teller"}',
                    'id' => 246,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:09',
                    'updated_at' => '2021-01-18 23:25:09',
                ),
                246 => 
                array (
                    'remote_id' => '681',
                    'name' => 'CPM Federal Credit Union',
                    'config' => '{"fid":"253279536","org":"USERS, Inc.","url":"https:\\/\\/cpm.usersonlnet.com\\/scripts\\/isaofx.dll"}',
                    'id' => 247,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:09',
                    'updated_at' => '2021-01-18 23:25:09',
                ),
                247 => 
                array (
                    'remote_id' => '682',
                    'name' => 'DATCU',
                    'config' => '{"fid":"311980725","org":"DATCU","url":"https:\\/\\/online.datcu.coop\\/ofxserver\\/ofxsrvr.dll"}',
                    'id' => 248,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:09',
                    'updated_at' => '2021-01-18 23:25:09',
                ),
                248 => 
                array (
                    'remote_id' => '683',
                    'name' => 'Denver Community Federal Credit Union',
                    'config' => '{"fid":"10524","org":"Denver Community FCU","url":"https:\\/\\/pccu.dcfcu.coop\\/ofx\\/ofx.dll"}',
                    'id' => 249,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:09',
                    'updated_at' => '2021-01-18 23:25:09',
                ),
                249 => 
                array (
                    'remote_id' => '684',
                    'name' => 'Discover Platinum',
                    'config' => '{"fid":"7102","org":"Discover Financial Services","url":"https:\\/\\/ofx.discovercard.com\\/"}',
                    'id' => 250,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:09',
                    'updated_at' => '2021-01-18 23:25:09',
                ),
                250 => 
                array (
                    'remote_id' => '685',
                    'name' => 'EAB',
                    'config' => '{"fid":"6505","org":"ISC","url":"https:\\/\\/www.oasis.cfree.com\\/fip\\/genesis\\/prod\\/06505.ofx"}',
                    'id' => 251,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:10',
                    'updated_at' => '2021-01-18 23:25:10',
                ),
                251 => 
                array (
                    'remote_id' => '686',
                    'name' => 'FAA Credit Union',
                    'config' => '{"fid":"114","org":"FAA Credit Union","url":"https:\\/\\/flightline.faaecu.org\\/ofx\\/ofx.dll"}',
                    'id' => 252,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:10',
                    'updated_at' => '2021-01-18 23:25:10',
                ),
                252 => 
                array (
                    'remote_id' => '687',
                    'name' => 'Fairwinds Credit Union',
                    'config' => '{"fid":"4842","org":"OSI 2","url":"https:\\/\\/OFXDownload.opensolutionsTOC.com\\/eftxweb\\/access.ofx"}',
                    'id' => 253,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:10',
                    'updated_at' => '2021-01-18 23:25:10',
                ),
                253 => 
                array (
                    'remote_id' => '688',
                    'name' => 'FedChoice FCU',
                    'config' => '{"fid":"254074785","org":"FEDCHOICE","url":"https:\\/\\/ofx.fedchoice.org\\/ofxserver\\/ofxsrvr.dll"}',
                    'id' => 254,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:10',
                    'updated_at' => '2021-01-18 23:25:10',
                ),
                254 => 
                array (
                    'remote_id' => '689',
                    'name' => 'First Clearing, LLC',
                    'config' => '{"fid":"10033","org":"First Clearing, LLC","url":"https:\\/\\/pfmpw.wachovia.com\\/cgi-forte\\/fortecgi?servicename=ofxbrk&pagename=PFM"}',
                    'id' => 255,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:10',
                    'updated_at' => '2021-01-18 23:25:10',
                ),
                255 => 
                array (
                    'remote_id' => '690',
                    'name' => 'First Citizens',
                    'config' => '{"fid":"1849","org":"First Citizens","url":"https:\\/\\/www.oasis.cfree.com\\/fip\\/genesis\\/prod\\/01849.ofx"}',
                    'id' => 256,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:10',
                    'updated_at' => '2021-01-18 23:25:10',
                ),
                256 => 
                array (
                    'remote_id' => '691',
                    'name' => 'First Hawaiian Bank',
                    'config' => '{"fid":"3501","org":"BancWest Corp","url":"https:\\/\\/olbp.fhb.com\\/ofx0001\\/ofx_isapi.dll"}',
                    'id' => 257,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:10',
                    'updated_at' => '2021-01-18 23:25:10',
                ),
                257 => 
                array (
                    'remote_id' => '692',
                    'name' => 'First National Bank of St. Louis',
                    'config' => '{"fid":"162","org":"81004601","url":"https:\\/\\/ofx.centralbancompany.com\\/ofxserver\\/ofxsrvr.dll"}',
                    'id' => 258,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:10',
                    'updated_at' => '2021-01-18 23:25:10',
                ),
                258 => 
                array (
                    'remote_id' => '693',
                    'name' => 'First Interstate Bank',
                    'config' => '{"fid":"092901683","org":"FIB","url":"https:\\/\\/ofx.firstinterstatebank.com\\/OFXServer\\/ofxsrvr.dll"}',
                    'id' => 259,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:10',
                    'updated_at' => '2021-01-18 23:25:10',
                ),
                259 => 
                array (
                    'remote_id' => '694',
                    'name' => 'Goldman Sachs',
                    'config' => '{"fid":"1234","org":"gs.com","url":"https:\\/\\/portfolio-ofx.gs.com:446\\/ofx\\/ofx.eftx"}',
                    'id' => 260,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:10',
                    'updated_at' => '2021-01-18 23:25:10',
                ),
                260 => 
                array (
                    'remote_id' => '695',
                    'name' => 'Hudson Valley FCU',
                    'config' => '{"fid":"10767","org":"Hudson Valley FCU","url":"https:\\/\\/internetbanking.hvfcu.org\\/ofx\\/ofx.dll"}',
                    'id' => 261,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:10',
                    'updated_at' => '2021-01-18 23:25:10',
                ),
                261 => 
                array (
                    'remote_id' => '696',
                    'name' => 'IBM Southeast Employees Federal Credit Union',
                    'config' => '{"fid":"1779","org":"IBM Southeast EFCU","url":"https:\\/\\/rb.ibmsecu.org\\/ofx\\/ofx.dll"}',
                    'id' => 262,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:10',
                    'updated_at' => '2021-01-18 23:25:10',
                ),
                262 => 
                array (
                    'remote_id' => '697',
                    'name' => 'Insight CU',
                    'config' => '{"fid":"10764","org":"Insight Credit Union","url":"https:\\/\\/secure.insightcreditunion.com\\/ofx\\/ofx.dll"}',
                    'id' => 263,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:11',
                    'updated_at' => '2021-01-18 23:25:11',
                ),
                263 => 
                array (
                    'remote_id' => '698',
                    'name' => 'Janney Montgomery Scott LLC',
                    'config' => '{"fid":"11326","org":"AFS","url":"https:\\/\\/jmsofx.automatedfinancial.com"}',
                    'id' => 264,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:11',
                    'updated_at' => '2021-01-18 23:25:11',
                ),
                264 => 
                array (
                    'remote_id' => '699',
                    'name' => 'JSC Federal Credit Union',
                    'config' => '{"fid":"10491","org":"JSC Federal Credit Union","url":"https:\\/\\/starpclegacy.jscfcu.org\\/ofx\\/ofx.dll"}',
                    'id' => 265,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:11',
                    'updated_at' => '2021-01-18 23:25:11',
                ),
                265 => 
                array (
                    'remote_id' => '700',
                    'name' => 'J.P. Morgan',
                    'config' => '{"fid":"4701","org":"ISC","url":"https:\\/\\/www.oasis.cfree.com\\/fip\\/genesis\\/prod\\/04701.ofx"}',
                    'id' => 266,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:11',
                    'updated_at' => '2021-01-18 23:25:11',
                ),
                266 => 
                array (
                    'remote_id' => '701',
                    'name' => 'J.P. Morgan Clearing Corp.',
                    'config' => '{"fid":"7315","org":"GCS","url":"https:\\/\\/ofxgcs.toolkit.clearco.com"}',
                    'id' => 267,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:11',
                    'updated_at' => '2021-01-18 23:25:11',
                ),
                267 => 
                array (
                    'remote_id' => '702',
                    'name' => 'M & T Bank',
                    'config' => '{"fid":"2601","org":"ISC","url":"https:\\/\\/www.oasis.cfree.com\\/fip\\/genesis\\/prod\\/02601.ofx"}',
                    'id' => 268,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:11',
                    'updated_at' => '2021-01-18 23:25:11',
                ),
                268 => 
                array (
                    'remote_id' => '703',
                    'name' => 'Marquette Banks',
                    'config' => '{"fid":"1301","org":"ISC","url":"https:\\/\\/www.oasis.cfree.com\\/fip\\/genesis\\/prod\\/01301.ofx"}',
                    'id' => 269,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:12',
                    'updated_at' => '2021-01-18 23:25:12',
                ),
                269 => 
                array (
                    'remote_id' => '704',
                    'name' => 'Mercer',
                    'config' => '{"fid":"8007527525","org":"PutnamDefinedContributions","url":"https:\\/\\/ofx.mercerhrs.com\\/eftxweb\\/access.ofx"}',
                    'id' => 270,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:12',
                    'updated_at' => '2021-01-18 23:25:12',
                ),
                270 => 
                array (
                    'remote_id' => '705',
                    'name' => 'Merrill Lynch Online Payment',
                    'config' => '{"fid":"7301","org":"ISC","url":"https:\\/\\/www.oasis.cfree.com\\/fip\\/genesis\\/prod\\/07301.ofx"}',
                    'id' => 271,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:12',
                    'updated_at' => '2021-01-18 23:25:12',
                ),
                271 => 
                array (
                    'remote_id' => '706',
                    'name' => 'Missoula Federal Credit Union',
                    'config' => '{"fid":"5097","org":"Missoula Federal Credit Union","url":"https:\\/\\/secure.missoulafcu.org\\/ofx\\/ofx.dll"}',
                    'id' => 272,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:12',
                    'updated_at' => '2021-01-18 23:25:12',
                ),
                272 => 
                array (
                    'remote_id' => '707',
                'name' => 'Morgan Stanley (Smith Barney)',
                    'config' => '{"fid":"5207","org":"Smithbarney.com","url":"https:\\/\\/ofx.smithbarney.com\\/app-bin\\/ofx\\/servlets\\/access.ofx"}',
                    'id' => 273,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:12',
                    'updated_at' => '2021-01-18 23:25:12',
                ),
                273 => 
                array (
                    'remote_id' => '708',
                    'name' => 'Nevada State Bank - OLD',
                    'config' => '{"fid":"5401","org":"ISC","url":"https:\\/\\/www.oasis.cfree.com\\/fip\\/genesis\\/prod\\/05401.ofx"}',
                    'id' => 274,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:12',
                    'updated_at' => '2021-01-18 23:25:12',
                ),
                274 => 
                array (
                    'remote_id' => '709',
                    'name' => 'New England Federal Credit Union',
                    'config' => '{"fid":"2104","org":"New England Federal Credit Union","url":"https:\\/\\/pcaccess.nefcu.com\\/ofx\\/ofx.dll"}',
                    'id' => 275,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:13',
                    'updated_at' => '2021-01-18 23:25:13',
                ),
                275 => 
                array (
                    'remote_id' => '710',
                    'name' => 'Norwest',
                    'config' => '{"fid":"4601","org":"ISC","url":"https:\\/\\/www.oasis.cfree.com\\/fip\\/genesis\\/prod\\/04601.ofx"}',
                    'id' => 276,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:13',
                    'updated_at' => '2021-01-18 23:25:13',
                ),
                276 => 
                array (
                    'remote_id' => '711',
                    'name' => 'Oppenheimer & Co. Inc.',
                    'config' => '{"fid":"125","org":"Oppenheimer","url":"https:\\/\\/ofx.opco.com\\/eftxweb\\/access.ofx"}',
                    'id' => 277,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:13',
                    'updated_at' => '2021-01-18 23:25:13',
                ),
                277 => 
                array (
                    'remote_id' => '712',
                    'name' => 'Oregon College Savings Plan',
                    'config' => '{"fid":"51498","org":"tiaaoregon","url":"https:\\/\\/ofx3.financialtrans.com\\/tf\\/OFXServer?tx=OFXController&cz=702110804131918&cl=b1908000027141704061413"}',
                    'id' => 278,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:13',
                    'updated_at' => '2021-01-18 23:25:13',
                ),
                278 => 
                array (
                    'remote_id' => '713',
                    'name' => 'RBC Dain Rauscher',
                    'config' => '{"fid":"8035","org":"RBC Dain Rauscher","url":"https:\\/\\/ofx.rbcdain.com\\/"}',
                    'id' => 279,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:13',
                    'updated_at' => '2021-01-18 23:25:13',
                ),
                279 => 
                array (
                    'remote_id' => '714',
                    'name' => 'Robert W. Baird & Co.',
                    'config' => '{"fid":"1109","org":"Robert W. Baird & Co.","url":"https:\\/\\/ofx.rwbaird.com"}',
                    'id' => 280,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:13',
                    'updated_at' => '2021-01-18 23:25:13',
                ),
                280 => 
                array (
                    'remote_id' => '715',
                    'name' => 'Sears Card',
                    'config' => '{"fid":"26810","org":"CITIGROUP","url":"https:\\/\\/secureofx.bankhost.com\\/tuxofx\\/cgi-bin\\/cgi_chip"}',
                    'id' => 281,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:13',
                    'updated_at' => '2021-01-18 23:25:13',
                ),
                281 => 
                array (
                    'remote_id' => '716',
                    'name' => 'South Trust Bank',
                    'config' => '{"fid":"6101","org":"ISC","url":"https:\\/\\/www.oasis.cfree.com\\/fip\\/genesis\\/prod\\/06101.ofx"}',
                    'id' => 282,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:13',
                    'updated_at' => '2021-01-18 23:25:13',
                ),
                282 => 
                array (
                    'remote_id' => '717',
                    'name' => 'Standard Federal Bank',
                    'config' => '{"fid":"6507","org":"ISC","url":"https:\\/\\/www.oasis.cfree.com\\/fip\\/genesis\\/prod\\/06507.ofx"}',
                    'id' => 283,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:13',
                    'updated_at' => '2021-01-18 23:25:13',
                ),
                283 => 
                array (
                    'remote_id' => '718',
                    'name' => 'United California Bank',
                    'config' => '{"fid":"2701","org":"ISC","url":"https:\\/\\/www.oasis.cfree.com\\/fip\\/genesis\\/prod\\/02701.ofx"}',
                    'id' => 284,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:14',
                    'updated_at' => '2021-01-18 23:25:14',
                ),
                284 => 
                array (
                    'remote_id' => '719',
                    'name' => 'United Federal CU - PowerLink',
                    'config' => '{"fid":"1908","org":"United Federal Credit Union","url":"https:\\/\\/remotebanking.unitedfcu.com\\/ofx\\/ofx.dll"}',
                    'id' => 285,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:14',
                    'updated_at' => '2021-01-18 23:25:14',
                ),
                285 => 
                array (
                    'remote_id' => '720',
                    'name' => 'VALIC',
                    'config' => '{"fid":"77019","org":"valic.com","url":"https:\\/\\/ofx.valic.com\\/eftxweb\\/access.ofx"}',
                    'id' => 286,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:14',
                    'updated_at' => '2021-01-18 23:25:14',
                ),
                286 => 
                array (
                    'remote_id' => '721',
                    'name' => 'Van Kampen Funds, Inc.',
                    'config' => '{"fid":"3625","org":"Van Kampen Funds, Inc.","url":"https:\\/\\/ofx3.financialtrans.com\\/tf\\/OFXServer?tx=OFXController&cz=702110804131918&cl=9210013100012150413"}',
                    'id' => 287,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:14',
                    'updated_at' => '2021-01-18 23:25:14',
                ),
                287 => 
                array (
                    'remote_id' => '722',
                    'name' => 'Vanguard Group',
                    'config' => '{"fid":"1358","org":"The Vanguard Group","url":"https:\\/\\/vesnc.vanguard.com\\/us\\/OfxProfileServlet"}',
                    'id' => 288,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:14',
                    'updated_at' => '2021-01-18 23:25:14',
                ),
                288 => 
                array (
                    'remote_id' => '723',
                    'name' => 'Velocity Credit Union',
                    'config' => '{"fid":"9909","org":"Velocity Credit Union","url":"https:\\/\\/rbserver.velocitycu.com\\/ofx\\/ofx.dll"}',
                    'id' => 289,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:14',
                    'updated_at' => '2021-01-18 23:25:14',
                ),
                289 => 
                array (
                    'remote_id' => '724',
                    'name' => 'Waddell & Reed - Ivy Funds',
                    'config' => '{"fid":"49623","org":"waddell","url":"https:\\/\\/ofx3.financialtrans.com\\/tf\\/OFXServer?tx=OFXController&cz=702110804131918&cl=722000303041111"}',
                    'id' => 290,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:14',
                    'updated_at' => '2021-01-18 23:25:14',
                ),
                290 => 
                array (
                    'remote_id' => '725',
                    'name' => 'Umpqua Bank',
                    'config' => '{"fid":"1001","org":"Umpqua","url":"https:\\/\\/ofx.umpquabank.com\\/ofx\\/process.ofx"}',
                    'id' => 291,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:14',
                    'updated_at' => '2021-01-18 23:25:14',
                ),
                291 => 
                array (
                    'remote_id' => '726',
                    'name' => 'Discover Bank',
                    'config' => '{"fid":"12610","org":"Discover Bank","url":"https:\\/\\/ofx.discovercard.com"}',
                    'id' => 292,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:15',
                    'updated_at' => '2021-01-18 23:25:15',
                ),
                292 => 
                array (
                    'remote_id' => '727',
                    'name' => 'Elevations Credit Union',
                    'config' => '{"fid":"1001","org":"uocfcu","url":"https:\\/\\/ofx.elevationscu.com\\/scripts\\/serverext.dll"}',
                    'id' => 293,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:15',
                    'updated_at' => '2021-01-18 23:25:15',
                ),
                293 => 
                array (
                    'remote_id' => '728',
                    'name' => 'Kitsap Community Credit Union',
                    'config' => '{"fid":"325180223","org":"Kitsap Community Federal Credit","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                    'id' => 294,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:15',
                    'updated_at' => '2021-01-18 23:25:15',
                ),
                294 => 
                array (
                    'remote_id' => '729',
                    'name' => 'Charles Schwab Retirement',
                    'config' => '{"fid":"1234","org":"SchwabRPS","url":"https:\\/\\/ofx.schwab.com\\/cgi_dev\\/ofx_server"}',
                    'id' => 295,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:15',
                    'updated_at' => '2021-01-18 23:25:15',
                ),
                295 => 
                array (
                    'remote_id' => '730',
                    'name' => 'Charles Schwab Retirement Plan Services',
                    'config' => '{"fid":"1234","org":"SchwabRPS","url":"https:\\/\\/ofx.schwab.com\\/cgi_dev\\/ofx_server"}',
                    'id' => 296,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:15',
                    'updated_at' => '2021-01-18 23:25:15',
                ),
                296 => 
                array (
                    'remote_id' => '731',
                    'name' => 'First Tech Federal Credit Union',
                    'config' => '{"fid":"3169","org":"First Tech Federal Credit Union","url":"https:\\/\\/ofx.firsttechfed.com"}',
                    'id' => 297,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:15',
                    'updated_at' => '2021-01-18 23:25:15',
                ),
                297 => 
                array (
                    'remote_id' => '732',
                    'name' => 'Affinity Plus Federal Credit Union',
                    'config' => '{"fid":"75","org":"Affinity Plus FCU","url":"https:\\/\\/hb.affinityplus.org\\/ofx\\/ofx.dll"}',
                    'id' => 298,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:16',
                    'updated_at' => '2021-01-18 23:25:16',
                ),
                298 => 
                array (
                    'remote_id' => '733',
                    'name' => 'Bank of George',
                    'config' => '{"fid":"122402366","org":"122402366","url":"https:\\/\\/ofx.internet-ebanking.com\\/CCOFXServer\\/servlet\\/TP_OFX_Controller"}',
                    'id' => 299,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:16',
                    'updated_at' => '2021-01-18 23:25:16',
                ),
                299 => 
                array (
                    'remote_id' => '734',
                    'name' => 'Franklin Templeton Investments',
                    'config' => '{"fid":"9444","org":"franklintempleton.com","url":"https:\\/\\/ofx.franklintempleton.com\\/eftxweb\\/access.ofx"}',
                    'id' => 300,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:16',
                    'updated_at' => '2021-01-18 23:25:16',
                ),
                300 => 
                array (
                    'remote_id' => '735',
                    'name' => 'ING Institutional Plan Services ',
                    'config' => '{"fid":"1289","org":"ing-usa.com","url":"https:\\/\\/ofx.ingplans.com\\/ofx\\/Server"}',
                    'id' => 301,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:16',
                    'updated_at' => '2021-01-18 23:25:16',
                ),
                301 => 
                array (
                    'remote_id' => '736',
                    'name' => 'Sterne Agee',
                    'config' => '{"fid":"2170","org":"AFS","url":"https:\\/\\/salofx.automatedfinancial.com"}',
                    'id' => 302,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:16',
                    'updated_at' => '2021-01-18 23:25:16',
                ),
                302 => 
                array (
                    'remote_id' => '737',
                    'name' => 'Wells Fargo Advisors',
                    'config' => '{"fid":"12748","org":"WF","url":"https:\\/\\/ofxdc.wellsfargo.com\\/ofxbrokerage\\/process.ofx"}',
                    'id' => 303,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:16',
                    'updated_at' => '2021-01-18 23:25:16',
                ),
                303 => 
                array (
                    'remote_id' => '738',
                    'name' => 'Community 1st Credit Union',
                    'config' => '{"fid":"325082017","org":"Community 1st Credit Union","url":"https:\\/\\/ib.comm1stcu.org\\/scripts\\/isaofx.dll"}',
                    'id' => 304,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:17',
                    'updated_at' => '2021-01-18 23:25:17',
                ),
                304 => 
                array (
                    'remote_id' => '740',
                    'name' => 'J.P. Morgan Private Banking',
                    'config' => '{"fid":"0417","org":"jpmorgan.com","url":"https:\\/\\/ofx.jpmorgan.com\\/jpmredirector"}',
                    'id' => 305,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:17',
                    'updated_at' => '2021-01-18 23:25:17',
                ),
                305 => 
                array (
                    'remote_id' => '741',
                    'name' => 'Northwest Community CU',
                    'config' => '{"fid":"1948","org":"Cavion","url":"https:\\/\\/ofx.lanxtra.com\\/ofx\\/servlet\\/Teller"}',
                    'id' => 306,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:17',
                    'updated_at' => '2021-01-18 23:25:17',
                ),
                306 => 
                array (
                    'remote_id' => '742',
                    'name' => 'North Carolina State Employees Credit Union',
                    'config' => '{"fid":"1001","org":"SECU","url":"https:\\/\\/onlineaccess.ncsecu.org\\/secuofx\\/secu.ofx "}',
                    'id' => 307,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:17',
                    'updated_at' => '2021-01-18 23:25:17',
                ),
                307 => 
                array (
                    'remote_id' => '743',
                    'name' => 'International Bank of Commerce',
                    'config' => '{"fid":"1001","org":"IBC","url":"https:\\/\\/ibcbankonline2.ibc.com\\/scripts\\/serverext.dll"}',
                    'id' => 308,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:17',
                    'updated_at' => '2021-01-18 23:25:17',
                ),
                308 => 
                array (
                    'remote_id' => '744',
                    'name' => 'RaboBank America',
                    'config' => '{"fid":"11540","org":"RBB","url":"https:\\/\\/ofx.rabobankamerica.com\\/ofx\\/process.ofx"}',
                    'id' => 309,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:17',
                    'updated_at' => '2021-01-18 23:25:17',
                ),
                309 => 
                array (
                    'remote_id' => '745',
                    'name' => 'Hughes Federal Credit Union',
                    'config' => '{"fid":"1951","org":"Cavion","url":"https:\\/\\/ofx.lanxtra.com\\/ofx\\/servlet\\/Teller"}',
                    'id' => 310,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:17',
                    'updated_at' => '2021-01-18 23:25:17',
                ),
                310 => 
                array (
                    'remote_id' => '746',
                    'name' => 'Apple FCU',
                    'config' => '{"fid":"256078514","org":"DI","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                    'id' => 311,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:18',
                    'updated_at' => '2021-01-18 23:25:18',
                ),
                311 => 
                array (
                    'remote_id' => '747',
                    'name' => 'Chemical Bank',
                    'config' => '{"fid":"072410013","org":"DI","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                    'id' => 312,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:18',
                    'updated_at' => '2021-01-18 23:25:18',
                ),
                312 => 
                array (
                    'remote_id' => '748',
                    'name' => 'Local Government Federal Credit Union',
                    'config' => '{"fid":"1001","org":"SECU","url":"https:\\/\\/onlineaccess.ncsecu.org\\/lgfcuofx\\/lgfcu.ofx"}',
                    'id' => 313,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:18',
                    'updated_at' => '2021-01-18 23:25:18',
                ),
                313 => 
                array (
                    'remote_id' => '749',
                    'name' => 'Wells Fargo Bank',
                    'config' => '{"fid":"3000","org":"WF","url":"https:\\/\\/ofxdc.wellsfargo.com\\/ofx\\/process.ofx"}',
                    'id' => 314,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:18',
                    'updated_at' => '2021-01-18 23:25:18',
                ),
                314 => 
                array (
                    'remote_id' => '750',
                    'name' => 'Schwab Retirement Plan Services',
                    'config' => '{"fid":"11811","org":"The 401k Company","url":"https:\\/\\/ofx1.401kaccess.com"}',
                    'id' => 315,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:19',
                    'updated_at' => '2021-01-18 23:25:19',
                ),
                315 => 
                array (
                    'remote_id' => '751',
                'name' => 'Southern Community Bank and Trust (SCB&T)',
                    'config' => '{"fid":"053112097","org":"MOneFortyEight","url":"https:\\/\\/ofx1.evault.ws\\/OFXServer\\/ofxsrvr.dll"}',
                    'id' => 316,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:19',
                    'updated_at' => '2021-01-18 23:25:19',
                ),
                316 => 
                array (
                    'remote_id' => '752',
                    'name' => 'Elevations Credit Union IB WC-DC',
                    'config' => '{"fid":"307074580","org":"uofcfcu","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx "}',
                    'id' => 317,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:19',
                    'updated_at' => '2021-01-18 23:25:19',
                ),
                317 => 
                array (
                    'remote_id' => '753',
                    'name' => 'Credit Suisse Securities USA LLC',
                    'config' => '{"fid":"001","org":"Credit Suisse Securities USA LLC","url":"https:\\/\\/ofx.netxclient.com\\/cgi\\/OFXNetx"}',
                    'id' => 318,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:19',
                    'updated_at' => '2021-01-18 23:25:19',
                ),
                318 => 
                array (
                    'remote_id' => '754',
                    'name' => 'North Country FCU',
                    'config' => '{"fid":"211691004","org":"DI","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                    'id' => 319,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:19',
                    'updated_at' => '2021-01-18 23:25:19',
                ),
                319 => 
                array (
                    'remote_id' => '755',
                    'name' => 'South Carolina Bank and Trust',
                    'config' => '{"fid":"053200983","org":"MZeroOneZeroSCBT","url":"https:\\/\\/ofx1.evault.ws\\/ofxserver\\/ofxsrvr.dll"}',
                    'id' => 320,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:19',
                    'updated_at' => '2021-01-18 23:25:19',
                ),
                320 => 
                array (
                    'remote_id' => '756',
                    'name' => 'Wings Financial',
                    'config' => '{"fid":"296076152","org":"DI","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                    'id' => 321,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:20',
                    'updated_at' => '2021-01-18 23:25:20',
                ),
                321 => 
                array (
                    'remote_id' => '757',
                    'name' => 'Haverhill Bank',
                    'config' => '{"fid":"93","org":"orcc","url":"https:\\/\\/www20.onlinebank.com\\/OROFX16Listener"}',
                    'id' => 322,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:20',
                    'updated_at' => '2021-01-18 23:25:20',
                ),
                322 => 
                array (
                    'remote_id' => '758',
                    'name' => 'Mission Federal Credit Union',
                    'config' => '{"fid":"1001","org":"mission","url":"https:\\/\\/missionlink.missionfcu.org\\/scripts\\/serverext.dll"}',
                    'id' => 323,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:20',
                    'updated_at' => '2021-01-18 23:25:20',
                ),
                323 => 
                array (
                    'remote_id' => '759',
                    'name' => 'Southwest Missouri Bank',
                    'config' => '{"fid":"101203641","org":"Jack Henry","url":"https:\\/\\/directline.netteller.com"}',
                    'id' => 324,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:20',
                    'updated_at' => '2021-01-18 23:25:20',
                ),
                324 => 
                array (
                    'remote_id' => '760',
                    'name' => 'Cambridge Savings Bank',
                    'config' => '{"fid":"211371120","org":"DI","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                    'id' => 325,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:20',
                    'updated_at' => '2021-01-18 23:25:20',
                ),
                325 => 
                array (
                    'remote_id' => '761',
                    'name' => 'NetxClient UAT',
                    'config' => '{"fid":"1023","org":"NetxClient","url":"https:\\/\\/uat-ofx.netxclient.inautix.com\\/cgi\\/OFXNetx"}',
                    'id' => 326,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:20',
                    'updated_at' => '2021-01-18 23:25:20',
                ),
                326 => 
                array (
                    'remote_id' => '762',
                    'name' => 'bankfinancial',
                    'config' => '{"fid":"271972899","org":"DI","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                    'id' => 327,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:21',
                    'updated_at' => '2021-01-18 23:25:21',
                ),
                327 => 
                array (
                    'remote_id' => '763',
                    'name' => 'AXA Equitable',
                    'config' => '{"fid":"7199","org":"AXA","url":"https:\\/\\/ofx.netxclient.com\\/cgi\\/OFXNetx"}',
                    'id' => 328,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:21',
                    'updated_at' => '2021-01-18 23:25:21',
                ),
                328 => 
                array (
                    'remote_id' => '764',
                    'name' => 'Premier America Credit Union',
                    'config' => '{"fid":"322283990","org":"DI","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                    'id' => 329,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:21',
                    'updated_at' => '2021-01-18 23:25:21',
                ),
                329 => 
                array (
                    'remote_id' => '765',
                    'name' => 'Bank of America - 5959',
                    'config' => '{"fid":"5959","org":"HAN","url":"https:\\/\\/ofx.bankofamerica.com\\/cgi-forte\\/fortecgi?servicename=ofx_2-3&pagename=ofx"}',
                    'id' => 330,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:21',
                    'updated_at' => '2021-01-18 23:25:21',
                ),
                330 => 
                array (
                    'remote_id' => '766',
                    'name' => 'First Command Bank',
                    'config' => '{"fid":"188","org":"First Command Bank","url":"https:\\/\\/www19.onlinebank.com\\/OROFX16Listener"}',
                    'id' => 331,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:21',
                    'updated_at' => '2021-01-18 23:25:21',
                ),
                331 => 
                array (
                    'remote_id' => '767',
                    'name' => 'TIAA-CREF',
                    'config' => '{"fid":"041","org":"tiaa-cref.org","url":"https:\\/\\/ofx.netxclient.com\\/cgi\\/OFXNetx"}',
                    'id' => 332,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:21',
                    'updated_at' => '2021-01-18 23:25:21',
                ),
                332 => 
                array (
                    'remote_id' => '768',
                    'name' => 'Citizens National Bank',
                    'config' => '{"fid":"111903151","org":"DI","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                    'id' => 333,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:21',
                    'updated_at' => '2021-01-18 23:25:21',
                ),
                333 => 
                array (
                    'remote_id' => '769',
                    'name' => 'Tower Federal Credit Union',
                    'config' => '{"fid":"255077370","org":"DI","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                    'id' => 334,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:21',
                    'updated_at' => '2021-01-18 23:25:21',
                ),
                334 => 
                array (
                    'remote_id' => '770',
                    'name' => 'First Republic Bank',
                    'config' => '{"fid":"321081669","org":"DI","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                    'id' => 335,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:21',
                    'updated_at' => '2021-01-18 23:25:21',
                ),
                335 => 
                array (
                    'remote_id' => '771',
                    'name' => 'Texans Credit Union',
                    'config' => '{"fid":"-1","org":"TexansCU","url":"https:\\/\\/www.netit.financial-net.com\\/ofx"}',
                    'id' => 336,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:22',
                    'updated_at' => '2021-01-18 23:25:22',
                ),
                336 => 
                array (
                    'remote_id' => '772',
                    'name' => 'AltaOne',
                    'config' => '{"fid":"322274462","org":"AltaOneFCU","url":"https:\\/\\/msconline.altaone.net\\/scripts\\/isaofx.dll"}',
                    'id' => 337,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:22',
                    'updated_at' => '2021-01-18 23:25:22',
                ),
                337 => 
                array (
                    'remote_id' => '773',
                    'name' => 'CenterState Bank',
                    'config' => '{"fid":"1942","org":"ORCC","url":"https:\\/\\/www20.onlinebank.com\\/OROFX16Listener"}',
                    'id' => 338,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:22',
                    'updated_at' => '2021-01-18 23:25:22',
                ),
                338 => 
                array (
                    'remote_id' => '774',
                    'name' => '5 Star Bank',
                    'config' => '{"fid":"307087713","org":"5 Star Bank","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                    'id' => 339,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:22',
                    'updated_at' => '2021-01-18 23:25:22',
                ),
                339 => 
                array (
                    'remote_id' => '775',
                    'name' => 'Belmont Savings Bank',
                    'config' => '{"fid":"211371764","org":"DI","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                    'id' => 340,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:22',
                    'updated_at' => '2021-01-18 23:25:22',
                ),
                340 => 
                array (
                    'remote_id' => '776',
                    'name' => 'UNIVERSITY & STATE EMPLOYEES CU',
                    'config' => '{"fid":"322281691","org":"DI","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                    'id' => 341,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:22',
                    'updated_at' => '2021-01-18 23:25:22',
                ),
                341 => 
                array (
                    'remote_id' => '777',
                    'name' => 'Wells Fargo Bank 2013',
                    'config' => '{"fid":"3001","org":"Wells Fargo","url":"https:\\/\\/www.oasis.cfree.com\\/3001.ofxgp"}',
                    'id' => 342,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:22',
                    'updated_at' => '2021-01-18 23:25:22',
                ),
                342 => 
                array (
                    'remote_id' => '778',
                    'name' => 'The Golden1 Credit Union',
                    'config' => '{"fid":"1001","org":"Golden1","url":"https:\\/\\/homebanking.golden1.com\\/scripts\\/serverext.dll"}',
                    'id' => 343,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:22',
                    'updated_at' => '2021-01-18 23:25:22',
                ),
                343 => 
                array (
                    'remote_id' => '779',
                    'name' => 'Woodsboro Bank',
                    'config' => '{"fid":"7479","org":"JackHenry","url":"https:\\/\\/directline.netteller.com\\/"}',
                    'id' => 344,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:22',
                    'updated_at' => '2021-01-18 23:25:22',
                ),
                344 => 
                array (
                    'remote_id' => '780',
                    'name' => 'Sandia Laboratory Federal Credit Union',
                    'config' => '{"fid":"1001","org":"SLFCU","url":"https:\\/\\/ofx-prod.slfcu.org\\/ofx\\/process.ofx "}',
                    'id' => 345,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:22',
                    'updated_at' => '2021-01-18 23:25:22',
                ),
                345 => 
                array (
                    'remote_id' => '781',
                    'name' => 'Oregon Community Credit Union',
                    'config' => '{"fid":"2077","org":"ORCC","url":"https:\\/\\/www20.onlinebank.com\\/OROFX16Listener"}',
                    'id' => 346,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:23',
                    'updated_at' => '2021-01-18 23:25:23',
                ),
                346 => 
                array (
                    'remote_id' => '782',
                    'name' => 'Advantis Credit Union',
                    'config' => '{"fid":"323075097","org":"DI","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                    'id' => 347,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:23',
                    'updated_at' => '2021-01-18 23:25:23',
                ),
                347 => 
                array (
                    'remote_id' => '783',
                    'name' => 'Capital One 360',
                    'config' => '{"fid":"031176110","org":"ING DIRECT","url":"https:\\/\\/ofx.capitalone360.com\\/OFXDownload\\/ofx.html"}',
                    'id' => 348,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:23',
                    'updated_at' => '2021-01-18 23:25:23',
                ),
                348 => 
                array (
                    'remote_id' => '784',
                    'name' => 'Flagstar Bank',
                    'config' => '{"fid":"272471852","org":"DI","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                    'id' => 349,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:23',
                    'updated_at' => '2021-01-18 23:25:23',
                ),
                349 => 
                array (
                    'remote_id' => '785',
                    'name' => 'Arizona State Credit Union',
                    'config' => '{"fid":"322172496","org":"DI","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                    'id' => 350,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:23',
                    'updated_at' => '2021-01-18 23:25:23',
                ),
                350 => 
                array (
                    'remote_id' => '786',
                    'name' => 'AmegyBank',
                    'config' => '{"fid":"1165","org":"292-3","url":"https:\\/\\/pfm.metavante.com\\/ofx\\/OFXServlet"}',
                    'id' => 351,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:23',
                    'updated_at' => '2021-01-18 23:25:23',
                ),
                351 => 
                array (
                    'remote_id' => '787',
                    'name' => 'Bank of Internet, USA',
                    'config' => '{"fid":"122287251","org":"Bank of Internet","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                    'id' => 352,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:23',
                    'updated_at' => '2021-01-18 23:25:23',
                ),
                352 => 
                array (
                    'remote_id' => '788',
                    'name' => 'Amplify Federal Credit Union',
                    'config' => '{"fid":"1","org":"Harland Financial Solutions","url":"https:\\/\\/ezonline.goamplify.com\\/ofx\\/ofx.dll"}',
                    'id' => 353,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:23',
                    'updated_at' => '2021-01-18 23:25:23',
                ),
                353 => 
                array (
                    'remote_id' => '789',
                    'name' => 'Capitol Federal Savings Bank',
                    'config' => '{"fid":"1001","org":"CapFed","url":"https:\\/\\/ofx-prod.capfed.com\\/ofx\\/process.ofx"}',
                    'id' => 354,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:24',
                    'updated_at' => '2021-01-18 23:25:24',
                ),
                354 => 
                array (
                    'remote_id' => '790',
                    'name' => 'Bank of America - access.ofx',
                    'config' => '{"fid":"5959","org":"HAN","url":"https:\\/\\/eftx.bankofamerica.com\\/eftxweb\\/access.ofx"}',
                    'id' => 355,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:24',
                    'updated_at' => '2021-01-18 23:25:24',
                ),
                355 => 
                array (
                    'remote_id' => '791',
                    'name' => 'SVB',
                    'config' => '{"fid":"944","org":"SVB","url":"https:\\/\\/ofx.svbconnect.com\\/eftxweb\\/access.ofx"}',
                    'id' => 356,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:24',
                    'updated_at' => '2021-01-18 23:25:24',
                ),
                356 => 
                array (
                    'remote_id' => '792',
                    'name' => 'Iinvestor360',
                    'config' => '{"fid":"7784","org":"Fidelity","url":"https:\\/\\/www.investor360.net\\/OFXDownload\\/FinService.asmx\\/GetData"}',
                    'id' => 357,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:24',
                    'updated_at' => '2021-01-18 23:25:24',
                ),
                357 => 
                array (
                    'remote_id' => '793',
                    'name' => 'Sound CU',
                    'config' => '{"fid":"325183220","org":"SOUNDCUDC","url":"https:\\/\\/mb.soundcu.com\\/OFXServer\\/ofxsrvr.dll"}',
                    'id' => 358,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:24',
                    'updated_at' => '2021-01-18 23:25:24',
                ),
                358 => 
                array (
                    'remote_id' => '794',
                'name' => 'Tangerine (Canada)',
                    'config' => '{"fid":"10951","org":"TangerineBank","url":"https:\\/\\/ofx.tangerine.ca"}',
                    'id' => 359,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:24',
                    'updated_at' => '2021-01-18 23:25:24',
                ),
                359 => 
                array (
                    'remote_id' => '795',
                    'name' => 'First Tennessee',
                    'config' => '{"fid":"2250","org":"Online Financial Services ","url":"https:\\/\\/ofx.firsttennessee.com\\/ofx\\/ofx_isapi.dll "}',
                    'id' => 360,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:24',
                    'updated_at' => '2021-01-18 23:25:24',
                ),
                360 => 
                array (
                    'remote_id' => '796',
                'name' => 'Alaska Air Visa (Bank of America)',
                    'config' => '{"fid":"1142","org":"BofA","url":"https:\\/\\/akairvisa.iglooware.com\\/visa.php"}',
                    'id' => 361,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:24',
                    'updated_at' => '2021-01-18 23:25:24',
                ),
                361 => 
                array (
                    'remote_id' => '797',
                    'name' => 'TIAA-CREF Retirement Services',
                    'config' => '{"fid":"1304","org":"TIAA-CREF","url":"https:\\/\\/ofx-service.tiaa-cref.org\\/public\\/ofx"}',
                    'id' => 362,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:24',
                    'updated_at' => '2021-01-18 23:25:24',
                ),
                362 => 
                array (
                    'remote_id' => '798',
                    'name' => 'Bofi federal bank',
                    'config' => '{"fid":"122287251","org":"Bofi Federal Bank - Business","url":"https:\\/\\/directline.netteller.com"}',
                    'id' => 363,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:24',
                    'updated_at' => '2021-01-18 23:25:24',
                ),
                363 => 
                array (
                    'remote_id' => '799',
                    'name' => 'Vanguard',
                    'config' => '{"fid":"15103","org":"Vanguard","url":"https:\\/\\/vesnc.vanguard.com\\/us\\/OfxDirectConnectServlet"}',
                    'id' => 364,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:25',
                    'updated_at' => '2021-01-18 23:25:25',
                ),
                364 => 
                array (
                    'remote_id' => '800',
                    'name' => 'Wright Patt CU',
                    'config' => '{"fid":"242279408","org":"DI","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                    'id' => 365,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:25',
                    'updated_at' => '2021-01-18 23:25:25',
                ),
                365 => 
                array (
                    'remote_id' => '801',
                    'name' => 'Technology Credit Union',
                    'config' => '{"fid":"15079","org":"TECHCUDC","url":"https:\\/\\/m.techcu.com\\/ofxserver\\/ofxsrvr.dll"}',
                    'id' => 366,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:25',
                    'updated_at' => '2021-01-18 23:25:25',
                ),
                366 => 
                array (
                    'remote_id' => '802',
                'name' => 'Capital One Bank (after 12-15-13)',
                    'config' => '{"fid":"1001","org":"Capital One","url":"https:\\/\\/ofx.capitalone.com\\/ofx\\/103\\/process.ofx"}',
                    'id' => 367,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:25',
                    'updated_at' => '2021-01-18 23:25:25',
                ),
                367 => 
                array (
                    'remote_id' => '803',
                    'name' => 'Bancorpsouth',
                    'config' => '{"fid":"1001","org":"BXS","url":"https:\\/\\/ofx-prod.bancorpsouthonline.com\\/ofx\\/process.ofx"}',
                    'id' => 368,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:25',
                    'updated_at' => '2021-01-18 23:25:25',
                ),
                368 => 
                array (
                    'remote_id' => '804',
                    'name' => 'Monterey Credit Union',
                    'config' => '{"fid":"2059","org":"orcc","url":"https:\\/\\/www20.onlinebank.com\\/OROFX16Listener"}',
                    'id' => 369,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:25',
                    'updated_at' => '2021-01-18 23:25:25',
                ),
                369 => 
                array (
                    'remote_id' => '805',
                    'name' => 'D. A. Davidson',
                    'config' => '{"fid":"59401","org":"dadco.com","url":"https:\\/\\/pfm.davidsoncompanies.com\\/eftxweb\\/access.ofx"}',
                    'id' => 370,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:25',
                    'updated_at' => '2021-01-18 23:25:25',
                ),
                370 => 
                array (
                    'remote_id' => '806',
                    'name' => 'Morgan Stanley ClientServ - Quicken Win Format',
                    'config' => '{"fid":"1235","org":"msdw.com","url":"https:\\/\\/ofx.morganstanleyclientserv.com\\/ofx\\/QuickenWinProfile.ofx"}',
                    'id' => 371,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:25',
                    'updated_at' => '2021-01-18 23:25:25',
                ),
                371 => 
                array (
                    'remote_id' => '807',
                    'name' => 'Star One Credit Union',
                    'config' => '{"fid":"321177968","org":"DI","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                    'id' => 372,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:26',
                    'updated_at' => '2021-01-18 23:25:26',
                ),
                372 => 
                array (
                    'remote_id' => '808',
                    'name' => 'Scottrade Brokerage',
                    'config' => '{"fid":"777","org":"Scottrade","url":"https:\\/\\/ofx.scottrade.com"}',
                    'id' => 373,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:26',
                    'updated_at' => '2021-01-18 23:25:26',
                ),
                373 => 
                array (
                    'remote_id' => '809',
                    'name' => 'Mutual Bank',
                    'config' => '{"fid":"88","org":"ORCC","url":"https:\\/\\/www20.onlinebank.com\\/OROFX16Listener"}',
                    'id' => 374,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:26',
                    'updated_at' => '2021-01-18 23:25:26',
                ),
                374 => 
                array (
                    'remote_id' => '810',
                    'name' => 'Affinity Plus Federal Credit Union-New',
                    'config' => '{"fid":"15268","org":"Affinity Plus Federal Credit Uni","url":"https:\\/\\/mobile.affinityplus.org\\/OFXDownload\\/OFXServer.aspx"}',
                    'id' => 375,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:26',
                    'updated_at' => '2021-01-18 23:25:26',
                ),
                375 => 
                array (
                    'remote_id' => '811',
                    'name' => 'Suncoast Credit Union',
                    'config' => '{"fid":"15469","org":"SunCoast","url":"https:\\/\\/ofx.suncoastcreditunion.com"}',
                    'id' => 376,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:26',
                    'updated_at' => '2021-01-18 23:25:26',
                ),
                376 => 
                array (
                    'remote_id' => '812',
                    'name' => 'Think Mutual Bank',
                    'config' => '{"fid":"10139","org":"JackHenry","url":"https:\\/\\/directline2.netteller.com"}',
                    'id' => 377,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:26',
                    'updated_at' => '2021-01-18 23:25:26',
                ),
                377 => 
                array (
                    'remote_id' => '813',
                    'name' => 'La Banque Postale',
                    'config' => '{"fid":"0","org":"0","url":"https:\\/\\/ofx.videoposte.com\\/"}',
                    'id' => 378,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:26',
                    'updated_at' => '2021-01-18 23:25:26',
                ),
                378 => 
                array (
                    'remote_id' => '814',
                    'name' => 'Pennsylvania State Employees Credit Union',
                    'config' => '{"fid":"231381116","org":"PENNSTATEEMPLOYEES","url":"https:\\/\\/directconnect.psecu.com\\/ofxserver\\/ofxsrvr.dll"}',
                    'id' => 379,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:27',
                    'updated_at' => '2021-01-18 23:25:27',
                ),
                379 => 
                array (
                    'remote_id' => '815',
                    'name' => 'St. Mary\'s Credit Union',
                    'config' => '{"fid":"211384214","org":"MSevenThirtySeven","url":"https:\\/\\/ofx1.evault.ws\\/OFXServer\\/ofxsrvr.dll"}',
                    'id' => 380,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:27',
                    'updated_at' => '2021-01-18 23:25:27',
                ),
                380 => 
                array (
                    'remote_id' => '816',
                    'name' => 'Institution For Savings',
                    'config' => '{"fid":"59466","org":"JackHenry","url":"https:\\/\\/directline2.netteller.com"}',
                    'id' => 381,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:27',
                    'updated_at' => '2021-01-18 23:25:27',
                ),
                381 => 
                array (
                    'remote_id' => '817',
                    'name' => 'PNC Online Banking',
                    'config' => '{"fid":"4501","org":"ISC","url":"https:\\/\\/www.oasis.cfree.com\\/4501.ofxgp"}',
                    'id' => 382,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:27',
                    'updated_at' => '2021-01-18 23:25:27',
                ),
                382 => 
                array (
                    'remote_id' => '818',
                    'name' => 'PNC Banking Online',
                    'config' => '{"fid":"4501","org":"ISC","url":"https:\\/\\/www.oasis.cfree.com\\/4501.ofx"}',
                    'id' => 383,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:27',
                    'updated_at' => '2021-01-18 23:25:27',
                ),
                383 => 
                array (
                    'remote_id' => '820',
                    'name' => 'Central Bank Utah',
                    'config' => '{"fid":"124300327","org":"DI","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                    'id' => 384,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:27',
                    'updated_at' => '2021-01-18 23:25:27',
                ),
                384 => 
                array (
                    'remote_id' => '821',
                    'name' => 'nuVision Financial FCU',
                    'config' => '{"fid":"322282399","org":"DI","url":"https:\\/\\/ofxdi.diginsite.com\\/cmr\\/cmr.ofx"}',
                    'id' => 385,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:27',
                    'updated_at' => '2021-01-18 23:25:27',
                ),
                385 => 
                array (
                    'remote_id' => '822',
                    'name' => 'Landings Credit Union',
                    'config' => '{"fid":"02114","org":"JackHenry","url":"https:\\/\\/directline.netteller.com"}',
                    'id' => 386,
                    'bank_library_ifd' => 1,
                    'created_at' => '2021-01-18 23:25:27',
                    'updated_at' => '2021-01-18 23:25:27',
                ),
            ));
        
        
    }
}