<?php
/**
 * Checkout Addresses Custom Attribute Installer.
 * @category: Magento
 * @package: Perficient/Checkout
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Checkout
 */

namespace Perficient\Checkout\Setup\Patch\Data;

use Magento\CheckoutAgreements\Api\Data\AgreementInterface;
use Magento\CheckoutAgreements\Model\AgreementFactory;
use Magento\CheckoutAgreements\Model\ResourceModel\Agreement as AgreementResource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class AddressCheckoutProcessConfigChanges
 * @package Perficient\Checkout\Setup\Patch\Data
 */
class AddressCheckoutProcessConfigChanges implements DataPatchInterface
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    final const SHOW_COMPANY = 'customer/address/company_show';
    final const SHOW_TELEPHONE = 'customer/address/telephone_show';
    final const ENABLE_AGREEMENTS = 'checkout/options/enable_agreements';
    final const WEBSITE_SCOPE_ID = 1;
    final const STORE_SCOPE_ID = 0;
    /**
     * @var AgreementFactory
     */
    private $agreementFactory;

    /**
     * AddressCheckoutProcessConfigChanges constructor.
     * @param WriterInterface $configWriter
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param AgreementFactory|null $agreementFactory
     * @param AgreementInterface $agreementInterface
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        protected WriterInterface $configWriter,
        protected ModuleDataSetupInterface $moduleDataSetup,
        private readonly AgreementInterface $agreementInterface,
        private readonly AgreementResource $agreementResource,
        private readonly ResourceConnection $resourceConnection,
        AgreementFactory $agreementFactory = null
    )
    {
        $this->agreementFactory = $agreementFactory ?:
            ObjectManager::getInstance()->get(AgreementFactory::class);
    }

    /**
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Run code inside patch script
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->operations();
    }

    /**
     * Enable
     */
    public function operations()
    {
        /*add term and condition*/
        $postData = ['name' => 'Terms and Conditions',
            'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;" data-decoded="true"><h3>Introduction</h3>
<p>This website is owned and operated by WENDOVER ART GROUP.  These terms and conditions govern your use of this website and all materials and information accessed or viewed via the website (the “Materials”); by using this website, you accept these terms and conditions in full and agree to be bound by them and by all applicable laws and regulations.  If you disagree with these terms and conditions or any part of these terms and conditions, you agree not use this website.</p>
<p>These terms and conditions were last modified on May 1, 2012.</p>
<h3>License to use website</h3>
<p>Unless otherwise expressly stated, everything that you read or see on the website and all Materials are copyrighted or otherwise protected and owned by WENDOVER ART GROUP or a third party who licensed or granted to WENDOVER ART GROUP the right to use such material.  Unless otherwise indicated, all logos, names and marks on the website are trademarks or service marks owned or used under license by WENDOVER ART GROUP; the use or misuse of any of these marks or other information is strictly prohibited.  Unless otherwise expressly stated, nothing that you read or see on the website nor any Materials may be copied or used except as provided in these terms and conditions or with the express prior written consent of WENDOVER ART GROUP.  WENDOVER ART GROUP or its third party providers own the intellectual property rights in the website and all Materials.  Subject to the license below, which is not a transfer of title, all of these intellectual property rights are reserved.</p>
<p>WENDOVER ART GROUP hereby grants you the right to view, download for caching purposes only, and print the Materials and pages from the website solely for your own personal or internal business use and only, with respect to restricted areas of the website (as explained below), if you are an approved registered user of Wendoverart.com.  No other permission is granted to you to print, copy, reproduce, distribute, license, transfer, sell, transmit, upload, download, store, display in public, alter, modify or create derivative works of the Materials or website.  Your use of the website and the Materials is subject to the restrictions set out below and elsewhere in these terms and conditions.</p>
<p>You shall not:</p>
<ul>
<li>Republish the Materials (including republication on another website);</li>
<li>Sell, rent or sub-license the Materials;</li>
<li>Show any Material in public without the prior express written consent of WENDOVER ART GROUP (whether for commercial or non-commercial purposes);</li>
<li>Reproduce, duplicate, copy, use or otherwise exploit any of the Materials for any commercial purpose;</li>
<li>Edit or otherwise modify any Materials;</li>
<li>Remove any copyright, trademark or other proprietary notations form the Materials;</li>
<li>Transfer the Materials to another person or “mirror” the Materials on any other server; or</li>
<li>Redistribute any of the Materials, except for content specifically and expressly made available for redistribution.</li>
</ul>
<p>Where Materials are specifically made available for redistribution, such Materials may only be redistributed within your organization.</p>
<h3>Acceptable use</h3>
<p>You shall not use this website in any way that causes, or may cause, damage to the website or impairment of the availability or accessibility of the website; that interferes with or disrupts the website or servers or networks connected to the website or disobey any requirements, procedures, policies or regulations of networks connected to the website; or in any way which is unlawful, illegal, fraudulent or harmful, or in connection with any unlawful, illegal, fraudulent or harmful purpose or activity.</p>
<p>You shall not use this website to copy, store, host, transmit, send, use, publish or distribute any material that either (a) consists of (or is linked to) any spyware, computer virus, Trojan horse, worm, keystroke logger, rootkit or other malicious computer software; (b) is unlawful, harmful, threatening, abusive, harassing, tortuous, defamatory, vulgar, obscene, libelous, invasive of another’s privacy, hateful, or racially, ethnically or otherwise objectionable; or (c) that you do not have the right to or infringes the rights of any other party.</p>
<p>You shall not conduct any systematic or automated data collection activities (including without limitation scraping, data mining, data extraction and data harvesting) on or in relation to this website without WENDOVER ART GROUP’s express written consent.</p>
<p>You shall not use this website to transmit or send unsolicited commercial communications (including, without limitation, advertising and promotional materials, junk mail, spam, chain letters, and pyramid schemes), to advertise or to solicit business; to impersonate any person or entity or falsely state or otherwise misrepresent yourself, your age, or your affiliation with any person or entity; to stalk or otherwise harass another; or to otherwise act in a manner that negatively affects other users’ ability to use the website.</p>
<p>You shall not use this website to forge headers or otherwise manipulate identifiers in order to disguise the origin of any of your content transmitted through the website.</p>
<p>You shall not use this website for any purposes related to marketing without WENDOVER ART GROUP’s express written consent.</p>
<p>Although we provide rules for user conduct and postings, we do not control and are not responsible for what users post, transmit, or share to the website and are not responsible for any offensive, inappropriate, obscene, unlawful or otherwise objectionable content you may encounter on the website or in connection with any other user’s content or third party materials.  We are not responsible for the conduct, whether online or offline, of any user of the website.  </p>
<h3>Restricted access</h3>
<p>Access to certain areas of this website is restricted.  WENDOVER ART GROUP reserves the right to restrict access to certain areas of this website, or indeed this entire website, at WENDOVER ART GROUP’s sole discretion and at any time and for any reason.  In addition, certain areas that are unrestricted today may become restricted in the future, and certain areas that are restricted today may become unrestricted in the future. </p>
<p>To request full access to the website, you may be required to complete a registration form.  If WENDOVER ART GROUP provides you with a user ID and password to enable you to access restricted areas of the website or other content or services, you shall ensure that the user ID and password are kept confidential and you will not share such information with any other person or entity.  You accept responsibility for all activities that occur under your account or password and such use shall be deemed to be use by you.  You will ensure that all use of your account fully complies with these terms and conditions.</p>
<p>WENDOVER ART GROUP may disable your user ID and password in WENDOVER ART GROUP’s sole discretion without notice or explanation.  Transfer of the account by you to any other person or entity is strictly prohibited.</p>
<h3>User content</h3>
<p>In these terms and conditions, “your user content” means material (including without limitation text, images, audio material, video material and audio-visual material) that you submit to the website, for whatever purpose.  You are solely responsible for your user content, including obtaining any necessary permissions to post your user content.  You are also responsible, at your sole cost and expense, for creating any backup copies of your user content.  Your user content is not proprietary or protected.</p>
<p>By posting your user content or otherwise providing it to WENDOVER ART GROUP, you automatically grant, and you represent and warrant that you have the right to grant, to WENDOVER ART GROUP a worldwide, irrevocable, non-exclusive, transferable, perpetual, royalty-free, fully paid license to access, display, process, use, reproduce, adapt, publish, translate, copy and distribute your user content in any existing or future media.  You also grant to WENDOVER ART GROUP the right to sub-license these rights, and the right to bring an action for infringement of these rights.  You understand and acknowledge that your user content may be displayed in a public fashion on the website or otherwise and because of that, you should not include any of your Personal Information (as defined below) in your user content.</p>
<p>Your user content shall not be, and you shall not post any of your user content that: (a) is or may be illegal or unlawful, offensive, or violate the rights, harm, or threaten the safety of other users, people or entities; (b) you did not create or that you do not have permission to post; (c) infringes any third party\'s rights; (d) is capable of giving rise to legal action whether against you or WENDOVER ART GROUP or a third party (in each case under any applicable law or agreement); or (e) violates these terms and conditions.</p>
<p>You shall not submit any user content to the website that is or has ever been the subject of any threatened or actual legal proceedings or other similar complaint.</p>
<p>WENDOVER ART GROUP reserves the right to edit or remove any material submitted to this website, or stored on WENDOVER ART GROUP’s servers, or hosted or published upon this website.</p>
<p>Notwithstanding WENDOVER ART GROUP’s rights under these terms and conditions in relation to user content, WENDOVER ART GROUP does not undertake to monitor the submission of such content to, or the publication of such content on, this website.</p>
<h3>Third party content and links</h3>
<p>The website may contain links to other sites on the Internet that are owned and operated by third parties (“Third Party Sites”).  WENDOVER ART GROUP is not responsible for the collection or use of your user content, your Personal Information, or any other information pertaining to you at such Third Party Sites.  Nor is WENDOVER ART GROUP responsible for any damages you may incur in your use of such Third Party Sites.  You use Third Party Sites at your own risk and WENDOVER ART GROUP disclaims all liability arising from your use of Third Party Sites or a third party’s use of your user content, Personal Information, or any other information pertaining to you.  WENDOVER ART GROUP has not reviewed all of the Third Party Sites linked to the website and is not responsible for the content of or any products or services offered through such Third Party Sites.  Inclusion on the website of any Third Party Site does not imply a recommendation or endorsement by WENDOVER ART GROUP.</p>
<h3>No warranties</h3>
<p>THE WEBSITE AND ALL MATERIALS ARE PROVIDED “AS IS” WITHOUT ANY REPRESENTATIONS OR WARRANTIES OF ANY KIND, EITHER EXPRESS OR IMPLIED, INCLUDING, WITHOUT LIMITATION, IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE AND ANY REPRESENTATIONS OR WARRANTIES OF NON-INFRINGEMENT. </p>
<p>Without prejudice to the generality of the foregoing paragraph, WENDOVER ART GROUP does not warrant and makes no representations that:</p>
<p>l  This website or Materials will be constantly available, or available at all, error-free, free from viruses or other harmful components, or that your access to them will be uninterrupted; or</p>
<p>l  The Materials are complete, true, accurate, suitable, non-misleading, or fulfil your purposes or intended goals.</p>
<p>Nothing on this website constitutes, or is meant to constitute, advice of any kind.  If you require advice in relation to any legal, financial or medical matter you should consult an appropriate professional.</p>
<h3>Limitations of liability</h3>
<p>TO THE FULLEST EXTENT PERMITTED BY APPLICABLE LAW, UNDER NO CIRCUMSTANCES WILL WENDOVER ART GROUP OR ITS AFFILIATES, SUBSIDIARIES, CONTRACTORS, OR THEIR EMPLOYEES HAVE ANY LIABILITY TO YOU FOR DIRECT, INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL OR EXEMPLARY DAMAGES, INCLUDING, BUT NOT LIMITED TO, DAMAGES FOR LOSS OF PROFITS, GOODWILL, USE, DATA, OR OTHER INTANGIBLE LOSSES (EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGES), WHETHER IN CONTRACT, NEGLIGENCE, TORT OR OTHERWISE, ARISING OUT OF OR IN CONNECTION WITH YOUR USE OF THE WEBSITE OR MATERIALS OR YOUR INABILITY TO USE THE WEBSITES, MATERIALS OR ANY OTHER SERVICES, OR ANY FAILURE OF PERFORMANCE, ERROR, OMISSION, INTERRUPTION, EFFECT, DELAY IN OPERATION OR TRANSMISSION, COMPUTER VIRUS, LINE SYSTEM FAILURE, LOSS OF DATA, OR LOSS OF USE RELATED TO THE WEBSITE OR ANY THIRD PARTY SITE.  IF YOU ARE DISSATISFIED WITH THE WEBSITE, THE MATERIALS, WENDOVER ART GROUP, OR THESE TERMS AND CONDITIONS, YOUR SOLE AND EXCLUSIVE REMEDY IS TO DISCONTINUE YOUR USE OF THE WEBSITE AND MATERIALS.</p>
<h3>Reasonableness</h3>
<p>By using this website, you agree that the exclusions and limitations of liability set out in this website disclaimer are reasonable. </p>
<p>If you do not think they are reasonable, you shall not use this website.</p>
<h3>Other parties</h3>
<p>You accept that, as a registered corporation, WENDOVER ART GROUP has an interest in limiting the personal liability of its officers and employees.  You agree that you will not bring any claim personally against WENDOVER ART GROUP’s officers or employees in respect of any losses you suffer in connection with the website.</p>
<p>Without prejudice to the foregoing paragraph, you agree that the limitations of warranties and liability set out in this website disclaimer will protect WENDOVER ART GROUP officers, employees, agents, subsidiaries, successors, assigns and sub-contractors as well as WENDOVER ART GROUP.</p>
<h3>Unenforceable provisions and waivers</h3>
<p>If any provision of these terms and conditions is, or is found to be, invalid, illegal, or unenforceable under applicable law, such provision will be deemed amended to conform to the applicable laws and the remainder of these terms and conditions shall remain in full force and effect.  The failure of WENDOVER ART GROUP to enforce any right against you shall not constitute a waiver of that right.</p>
<h3>Indemnity</h3>
<p>You agree to fully indemnify and hold harmless WENDOVER ART GROUP and all of its affiliates and subsidiaries and each of their respective directors, officers, employees, vendors, contractors, customers, and agents (the “Indemnified Parties”) from and against any and all losses, damages, costs, liabilities and expenses (including, without limitation, legal expenses and attorneys’ fees and any amounts paid by an Indemnified Party to a third party in settlement of a claim or dispute) incurred or suffered by an Indemnified Party arising out of or relating to any breach (or any claim of a breach) by you of any provision of these terms and conditions or arising out of or relating to your user content, regardless of whether your user content complies with these terms and conditions or not.</p>
<h3>Collection of information</h3>
<p>You may be asked to voluntarily provide your name, address, e-mail address, phone number, postal address (including your city, state, and zip code), tax identification number, credit card information, and/or other personally identifiable information ("Personal Information") to have access to some features of the website.  By using the website and providing WENDOVER ART GROUP with your Personal Information, you consent to WENDOVER ART GROUP’s use of such information as described in these terms and conditions.  You may always refuse to provide your Personal Information, but this may lead to WENDOVER ART GROUP’s inability to provide you with certain access or services.  WENDOVER ART GROUP or its business partners may also collect information that is anonymous, such as your IP number (a number used to identify your computer on the Internet) or the type of browser you are using ("Anonymous Information"), through the use of cookies or by other means.  We hope that, by using Anonymous Information, we can update the website to make it more useful to you and other users.  WENDOVER ART GROUP reserves the right to maintain, update, disclose or otherwise use Anonymous Information, without limitation.</p>
<h3>Use of information</h3>
<p>WENDOVER ART GROUP uses your Personal Information for the following purposes:  to administer your account; to administer and improve the website and related services; to notify you of products, services, promotional events or special offers that may be of interest to you; to provide Internet security; to respond to your inquiries; to process your purchase of and payment for certain products and services via the website; and to meet legal requirements.  WENDOVER ART GROUP may work with other businesses that may perform certain functions on its behalf, such as (but not limited to) sending e-mail messages, managing data, hosting the website, or providing customer service.  These business partners have access to your Personal Information only to the extent necessary to perform these specific functions and may not use it for any other purpose.  Additionally, sometimes WENDOVER ART GROUP will feel so strongly about the quality of products or services of a business partner that WENDOVER ART GROUP may share your Personal Information with that partner. If you do not wish for your Personal Information to be included in WENDOVER ART GROUP’s communications with these partners, you can make a written request by email sent to <a href="mailto:info@wendoverart.com">info@wendoverart.com</a> or write to WENDOVER ART GROUP at the address below.  Other than for the specific purposes set forth in these terms and conditions, WENDOVER ART GROUP will not disclose your Personal Information unless it obtains your prior consent or WENDOVER ART GROUP is legally required to do so.  If WENDOVER ART GROUP is involved in the sale of a substantial portion of its business assets, Anonymous or Personal Information may be among the transferred assets.</p>
<h3>Retention</h3>
<p>WENDOVER ART GROUP retains Personal Information for as long as the information is relevant to WENDOVER ART GROUP’s business purposes or until you request the removal of the data by contacting WENDOVER ART GROUP at <a href="mailto:info@wendoverart.com">info@wendoverart.com</a>.</p>
<h3>Privacy of children</h3>
<p>WENDOVER ART GROUP encourages parents and guardians to be aware of and participate in their children’s online activities.  We strictly adhere to the Children’s Online Privacy Protection Act (COPPA) and will not knowingly collect, use or disclose Personal Information from any child under the age of 13 in any manner that violates this law.</p>
<h3>Cookies</h3>
<p>When you visit the website, WENDOVER ART GROUP or a third party service provider may store or recognize some information on your computer in the form of a "cookie" or similar file that can help WENDOVER ART GROUP in many ways.  For example, cookies allow WENDOVER ART GROUP to tailor the website to better match your interests and preferences.  With most Internet browsers or other software, you can erase cookies from your computer hard drive, block all cookies or receive a warning before a cookie is stored.  Please refer to your browser instructions to learn more about these functions.  If you reject cookies, functionality of the website may be limited, and you may not be able to take advantage of some of the website\'s features.</p>
<h3>Security</h3>
<p>WENDOVER ART GROUP has policies and procedures in place to protect the privacy and confidentiality of your Personal Information that WENDOVER ART GROUP collects and maintains.  All Personal Information is stored on secured servers.  Any unauthorized user of the website may result in criminal and/or civil prosecution.  If you place an order on the website, the website encrypts the credit/debit card number you submit prior to transmission over the Internet using secure socket layer (SSL) encryption technology with 256 bit encryption.  However, no transmission of data over the Internet is guaranteed to be 100% secure, and although WENDOVER ART GROUP will take reasonable steps to secure your Personal Information pursuant to these terms and conditions, WENDOVER ART GROUP cannot and does not guarantee the security of the information you send.<em> </em></p>
<h3>Breaches of these terms and conditions</h3>
<p>Without prejudice to WENDOVER ART GROUP’s other rights under these terms and conditions, if you breach these terms and conditions in any way, WENDOVER ART GROUP may take such action as WENDOVER ART GROUP deems appropriate to deal with the breach, including suspending your access to the website, prohibiting you from accessing the website, blocking computers using your IP address from accessing the website, contacting your Internet service provider to request that they block your access to the website and/or bringing court proceedings against you.  In addition, even if you do not breach these terms and conditions, we may terminate these terms and conditions, terminate or suspend your access to all or part of the website and Materials, or terminate or suspend your account, at any time, without notice to you and for any reason, including convenience.</p>
<h3>Disputes</h3>
<p>YOU AGREE THAT THE SOLE AND EXCLUSIVE FORUM AND REMEDY FOR ANY AND ALL DISPUTES AND CLAIMS RELATING IN ANY WAY TO OR ARISING OUT OF THESE TERMS AND CONDITIONS, THE WEBSITE, THE MATERIALS, YOUR USER CONTENT, THIRD PARTY SITES OR ANY OTHER THIRD PARTY MATERIALS (INCLUDING YOUR VISIT TO OR USE OF THE WEBSITE) SHALL BE FINAL AND BINDING ARBITRATION, except that, to the extent that either of your or WENDOVER ART GROUP has in any manner infringed upon or violated or threatened to infringe upon or violate the other party’s patent, copyright, trademark or trade secret rights, or you have otherwise violated any of the user conduct rules set forth above, then the parties acknowledge that arbitration is not an adequate remedy at law and that injunctive or other appropriate relief may be sought before, during or after the pendency of any arbitration proceeding brought pursuant to these terms and conditions, or in lieu of such proceedings. Arbitration under these terms and conditions shall be conducted by the American Arbitration Association (the “AAA”) under its Commercial Arbitration Rules and, in the case of consumer disputes, the AAA’s Supplementary Procedures for Consumer Related Disputes (the “AAA Consumer Rules”) (collectively the “AAA Rules”). The location of the arbitration and the allocation of costs and fees for such arbitration shall be determined in accordance with such AAA Rules and shall be subject to the limitations provided for in the AAA Consumer Rules (for consumer disputes) and in these terms and conditions. In rendering a decision, the arbitration panel shall follow the law of the United States and of the State of Florida, and shall not use equitable or other principles which would permit the panel to ignore these terms and conditions or the law.  The arbitration panel’s award shall be binding and may be entered as a judgment in any court of competent jurisdiction, provided, however, that errors of law may be appealed to a court of competent jurisdiction for review. Any award in arbitration shall be subject to all dollar and other limitations set forth in these terms and conditions. To the fullest extent permitted by applicable law, NO ARBITRATION OR CLAIM UNDER THESE TERMS AND CONDITIONS SHALL BE JOINED TO ANY OTHER ARBITRATION OR CLAIM, INCLUDING ANY ARBITRATION OR CLAIM INVOLVING ANY OTHER CURRENT OR FORMER USER OF THE WEBSITE, AND NO CLASS ARBITRATION PROCEEDINGS SHALL BE PERMITTED. In no event shall any claim, action or proceeding by you related in any way to the website (including your visit to or use of the website) be instituted more than one (1) year after the cause of action arose.</p>
<h3>Your opportunity to opt out of e-mail advertising</h3>
<p>To the extent WENDOVER ART GROUP intends to conduct any e-mail advertising and you do not wish to receive such promotional e-mail from WENDOVER ART GROUP, follow the opt-out instructions contained within the body any e-mail you receive.</p>
<h3>United States only</h3>
<p>The website and all Material is provided solely for the purpose of promoting WENDOVER ART GROUP’s operations in the United States and its territories.  WENDOVER ART GROUP makes no representation that the website or the Material is appropriate or available for use in other locations.  If despite these conditions, you use the website or Materials from outside the United States, you are solely responsible for compliance with any applicable local laws.</p>
<h3>E-Mail communications</h3>
<p>When you send an email to WENDOVER ART GROUP or communicate with WENDOVER ART GROUP via its online forms, you are communicating with WENDOVER ART GROUP electronically.  You thereby consent to receive communications from WENDOVER ART GROUP electronically; however, please note that e-mail communications are not necessarily secure or confidential.  For instance, it is possible that information transmitted to WENDOVER ART GROUP via e-mail may be read or obtained by other parties.  All agreements, notices, disclosures and other communications that WENDOVER ART GROUP provides to you electronically satisfy any legal requirement that such communications be in writing.</p>
<h3>Variation</h3>
<p>WENDOVER ART GROUP may revise these terms and conditions from time-to-time.  Revised terms and conditions will apply to the use of this website from the date of the publication of the revised terms and conditions on this website.  Please check this page regularly to ensure you are familiar with the current version.</p>
<h3>Assignment</h3>
<p>WENDOVER ART GROUP may transfer or assign these terms and conditions or its rights hereunder or sub-contract its duties hereunder to any third party without notice to or consent from you.  WENDOVER ART GROUP may also otherwise deal with WENDOVER ART GROUP’s rights and/or obligations under these terms and conditions in any manner it chooses without notifying you or obtaining your consent.</p>
<p>You may not transfer, assign, sub-contract or otherwise deal with your rights and/or obligations under these terms and conditions without the express prior written consent of WENDOVER ART GROUP. </p>
<h3>Entire agreement</h3>
<p>These terms and conditions constitute the entire agreement between you and WENDOVER ART GROUP in relation to your use of this website, and supersede all previous agreements in respect of your use of this website.</p>
<h3>Law and jurisdiction</h3>
<p>These terms and conditions will be governed by and construed in accordance with the laws of The State Of Florida.  Notwithstanding the preceding, all disputes concerning patent, federal trademark, or federal copyright matters shall be governed by federal law. </p>
<h3>Contacting WENDOVER ART GROUP</h3>
<p>Any questions regarding these terms and conditions may be directed to: <a href="mailto:info@wendoverart.com">info@wendoverart.com</a> or Wendover Art Group, <span>6465 126th Avenue North</span>, <span>Largo, FL  33773</span></p> </div></div></div>',
            'checkbox_text' => 'I agree with terms and conditions',
            'is_active' => self::WEBSITE_SCOPE_ID,
            'is_html' => self::WEBSITE_SCOPE_ID,
            'mode' => self::WEBSITE_SCOPE_ID,
        ];
        $model = $this->agreementFactory->create();
        $model->setData($postData);
        $model->save();
        $checkout_agreement_store_data = [
            'agreement_id' => $model->getId(),
            'store_id' => self::STORE_SCOPE_ID
        ];
        $this->resourceConnection->getConnection()->insert('checkout_agreement_store', $checkout_agreement_store_data);
        /*configuration settings*/
        $value = null;
        $this->configWriter->save(self::SHOW_COMPANY, $value, ScopeInterface::SCOPE_WEBSITES, self::WEBSITE_SCOPE_ID);
        $this->configWriter->save(self::SHOW_TELEPHONE, $value, ScopeInterface::SCOPE_WEBSITES, self::WEBSITE_SCOPE_ID);
        $this->configWriter->save(self::ENABLE_AGREEMENTS, self::WEBSITE_SCOPE_ID, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::STORE_SCOPE_ID);
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}