<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Http\Helpers\ApiCall;
use App\Http\Helpers\WebsiteOptionsApi;
use Illuminate\Support\Facades\Crypt;

class SubmitController extends Controller
{
    public function submitContactForm(Request $request) {

        // 'prohibited' validation rule does not work!!!'
        if($request->get('valkuil') || $request->get('valstrik')) return abort(404);

        $toValidate = array(
            'Naam' => 'required',
            'E-mail_adres' => 'required|email',
            'Bericht' => 'required',
            'Accept_conditions' => 'required',
        );
        $validationMessages = array(
            'Naam.required'=> 'Please provide a name',
            'E-mail_adres.required'=> 'Please provide an e-mail address',
            'E-mail_adres.email'=> 'The e-mail address is not correctly formed',
            'Bericht.required'=> 'Please fill in a message',
            'Accept_conditions.required'=> 'Please read and accept the general conditions',
        );
        /***********************************************************************************
            redirect()->back() / url()->previous() WERKT NIET!!! ---> strict-origin-when-cross-origin (referrer-policy)
            alleen redirect('/contact') gebruiken (bijvoorbeeld)
                OF bij dynamische paginas de huidige gebruiken: url()->current()
                    OF op de server de referrer-policy aanpassen van de vhost, naar bijvoorbeeld no-referrer-when-downgrade
        ************************************************************************************/
        // $validated = $request->validate($toValidate,$validationMessages);
        $validator = Validator::make($request->all(), $toValidate, $validationMessages);
        if($validator->fails()) {
            return redirect('/contact')->withErrors($validator)->withInput();
            // return redirect()->back()->withErrors($validator)->withInput();
            // return back()->withErrors($validator)->withInput();
        }

        $allWebsiteOptions = new WebsiteOptionsApi();
        $websiteOptions = $allWebsiteOptions->get();

        $to_email = $websiteOptions->email_address;
        // $to_email = 'leon@wtmedia-events.nl';
        // $to_email = 'frans@tamatta.org, rense@tamatta.org';
        // $subject = 'Ingevuld contactformulier vanaf rsmarine.eu';
        $subjectCompany = 'Ingevuld contactformulier vanaf glomaroffshore.com';
        $subjectVisitor = 'Copy of your message to glomaroffshore.com';
        
        $messages = $this->getHtmlEmails($request->all(), url('statics/email/logo.png'), 'De volgende gegevens zijn achtergelaten door de bezoeker.', 'Thanks for your message. We received the following information:');

        $headers = array(
            "MIME-Version: 1.0",
            "Content-Type: text/html; charset=ISO-8859-1",
            "From: Glomar Offshore <contactform@glomaroffshore.com>",
            "Reply-To: info@glomaroffshore.com",
            // "X-Priority: 1",
        );
        $headers = implode("\r\n", $headers);
        mail($to_email, $subjectCompany, $messages[0], $headers);
        mail($request->get('E-mail_adres'), $subjectVisitor, $messages[1], $headers);
        // mail($to_email, $subject, $message, $headers);
        // mail($to_email, $subject, $message);
        // return back()->with('success', 'Bedankt dat u contact met ons heeft opgenomen, we zullen uw bericht zo snel mogelijk in behandeling nemen!');
        return redirect('/contact')->with('success', 'contact');
    }
    public function submitSubscriptionForm(Request $request) {

        // 'prohibited' validation rule does not work!!!'
        if($request->get('valkuil') || $request->get('valstrik')) return abort(404);

        $toValidate = array(
            'Email' => 'required|email',
        );
        $validationMessages = array(
            'Email.required'=> 'Vul een e-mail adres in',
            'Email.email'=> 'Het e-mail adres is niet juist geformuleerd',
        );
        /***********************************************************************************
            redirect()->back() / url()->previous() WERKT NIET!!! ---> strict-origin-when-cross-origin (referrer-policy)
            alleen redirect('/contact') gebruiken (bijvoorbeeld)
                OF bij dynamische paginas de huidige gebruiken: url()->current()
                    OF op de server de referrer-policy aanpassen van de vhost, naar bijvoorbeeld no-referrer-when-downgrade
        ************************************************************************************/
        // $validated = $request->validate($toValidate,$validationMessages);
        $validator = Validator::make($request->all(), $toValidate, $validationMessages);
        if($validator->fails()) {
            return redirect(route('home'))->withErrors($validator)->withInput();
        }

        $allWebsiteOptions = new WebsiteOptionsApi();
        $websiteOptions = $allWebsiteOptions->get();

        // $to_email = $websiteOptions->email_address;
        $to_email = 'leon@wtmedia-events.nl';
        // $to_email = 'frans@tamatta.org, rense@tamatta.org';
        $subjectCompany = 'Ingevuld aanmeld-formulier vanaf wtmedia-events.nl';
        $subjectVisitor = 'Kopie van uw bericht aan wtmedia-events.nl';
        
        $messages = $this->getHtmlEmails($request->all(), url('statics/email/logo.png'), 'De volgende gegevens zijn achtergelaten door de bezoeker.', 'Bedankt voor uw bericht. De volgende informatie hebben we ontvangen:');

        $headers = array(
            "MIME-Version: 1.0",
            "Content-Type: text/html; charset=ISO-8859-1",
            "From: WT Media & Events <aanmeld-formulier@wtmedia-events.nl>",
            "Reply-To: support@wtmedia-events.nl",
            // "X-Priority: 1",
        );
        $headers = implode("\r\n", $headers);
        mail($to_email, $subjectCompany, $messages[0], $headers);
        mail($request->get('Email'), $subjectVisitor, $messages[1], $headers);
        // mail($to_email, $subject, $message, $headers);
        // mail($to_email, $subject, $message);
        // return back()->with('success', 'Bedankt dat u contact met ons heeft opgenomen, we zullen uw bericht zo snel mogelijk in behandeling nemen!');
        return redirect(route('home'))->with('success', 'subscription');
    }
    public function submitScheduleCallForm(Request $request) {

        // 'prohibited' validation rule does not work!!!'
        if($request->get('valkuil') || $request->get('valstrik')) return abort(404);

        $toValidate = array(
            'email' => 'required|email',
            'name' => 'required',
            'phone' => 'required',
        );
        $validationMessages = array(
            'email.required'=> 'Vul een e-mail adres in',
            'email.email'=> 'Het e-mail adres is niet juist geformuleerd',
            'name.required'=> 'Vul een naam in',
            'phone.required'=> 'Vul een telefoonnummer in',
        );
        /***********************************************************************************
            redirect()->back() / url()->previous() WERKT NIET!!! ---> strict-origin-when-cross-origin (referrer-policy)
            alleen redirect('/contact') gebruiken (bijvoorbeeld)
                OF bij dynamische paginas de huidige gebruiken: url()->current()
                    OF op de server de referrer-policy aanpassen van de vhost, naar bijvoorbeeld no-referrer-when-downgrade
        ************************************************************************************/
        // $validated = $request->validate($toValidate,$validationMessages);
        $validator = Validator::make($request->all(), $toValidate, $validationMessages);
        if($validator->fails()) {
            // return redirect(route('home'))->withErrors($validator)->withInput();
            return back()->withErrors($validator)->withInput();
        }

        $allWebsiteOptions = new WebsiteOptionsApi();
        $websiteOptions = $allWebsiteOptions->get();

        // $to_email = $websiteOptions->email_address;
        // $to_email = 'leon@wtmedia-events.nl';
        $to_email = Crypt::decryptString($request->get('email_to'));
        // $to_email = 'frans@tamatta.org, rense@tamatta.org';
        $subjectCompany = 'Ingevuld schedule-call-formulier vanaf wtmedia-events.nl';
        $subjectVisitor = 'Kopie van uw bericht aan wtmedia-events.nl';
        
        $messages = $this->getHtmlEmails($request->all(), url('statics/email/logo.png'), 'De volgende gegevens zijn achtergelaten door de bezoeker.', 'Bedankt voor uw bericht. De volgende informatie hebben we ontvangen:');

        $headers = array(
            "MIME-Version: 1.0",
            "Content-Type: text/html; charset=ISO-8859-1",
            "From: WT Media & Events <schedule-call-form@wtmedia-events.nl>",
            "Reply-To: support@wtmedia-events.nl",
            // "X-Priority: 1",
        );
        $headers = implode("\r\n", $headers);
        mail($to_email, $subjectCompany, $messages[0], $headers);
        mail($request->get('email'), $subjectVisitor, $messages[1], $headers);
        // mail($to_email, $subject, $message, $headers);
        // mail($to_email, $subject, $message);
        // return back()->with('success', 'Bedankt dat u contact met ons heeft opgenomen, we zullen uw bericht zo snel mogelijk in behandeling nemen!');
        // return redirect(route('home'))->with('success', 'subscription');
        return back()->with('success', Crypt::decryptString($request->get('success_text')));
    }
    public function submitBestellenForm(Request $request) {
        $validated = $request->validate([
            'Betreft' => 'required',
            'Bedrijfsnaam' => 'required',
            'Contactpersoon' => 'required',
            'Emailadres' => 'required|email',
            'Bericht' => 'required',
        ],[
            'Betreft.required'=> 'Geef a.u.b. de reden van toenadering aan.',
            'Bedrijfsnaam.required'=> 'Geef a.u.b. een bedrijfsnaam op.',
            'Contactpersoon.required'=> 'Geef a.u.b. een contactpersoon op.',
            'Emailadres.required'=> 'Geef a.u.b. een e-mail adres op.',
            'Emailadres.email'=> 'Het e-mail adres is niet juist geformuleerd.',
            'Bericht.required'=> 'Er is geen bericht ingevoerd.',
        ]);

        $to_email = 'leon.kuijf@gmail.com';
        // $to_email = 'frans@tamatta.org, rense@tamatta.org';
        $subject = 'Ingevuld bestelformulier vanaf ......nl';
        $message = 'De volgende informatie is verzonden:
        
            Betreft: ' . $request->get('Betreft') . '
            Bedrijfsnaam: ' . $request->get('Bedrijfsnaam') . '
            Contactpersoon: ' . $request->get('Contactpersoon') . '
            Email adres: ' . $request->get('Emailadres') . '
            Bericht: ' . $request->get('Bericht') . '
            ';

        $headers = array(
            "From: bestelformulier@.....nl",
            "MIME-Version: 1.0",
            "Content-Type: text/html; charset=ISO-8859-1",
            "X-Priority: 1",
        );
        $headers = implode("\r\n", $headers);
        // mail($to_email, $subject, $message, $headers);
        mail($to_email, $subject, $message);
        return back()->with('success', 'Bedankt dat u contact met ons heeft opgenomen, we zullen uw bericht zo snel mogelijk in behandeling nemen!');
    }
    public function getHtmlEmails($values, $imgLocation, $introTextCompany, $introTextVisitor) {
        $message1 = '';
        $message2 = '';
        $topHtml = '
        <html><body>
        <!--[if mso]>
        <table cellpadding="0" cellspacing="0" border="0" style="padding:0px;margin:0px;width:100%;">
            <tr>
                <td style="padding:0px;margin:0px;">&nbsp;</td>
                <td style="padding:0px;margin:0px;" width="500">
        <![endif]-->
                    <div style="
                        max-width: 500px;
                        padding: 20px;
                        font-family: verdana, arial;
                        font-size: 14px;
                        margin-left: auto;
                        margin-right: auto;
                        background-color: #FFF;
                        border: 1px solid #CCC;
                    ">
                    <p style="text-align:center;"><img src="' . $imgLocation . '" alt="JusBros logo" /></p>
        ';

        $messageCompany = '';
        $messageVisitor = '';
        foreach($values as $i => $v) {
            if($i == '_token' || $i == 'g-recaptcha-response' || $i == 'method' || $i == 'valstrik' || $i == 'valkuil' || $i == 'success_text' || $i == 'email_to') continue;
            $messageCompany .= '
            <p>
                ' . str_replace('_', ' ', $i) . ':<br />
                <strong>' . (trim($v) == ''?'-':$v) . '</strong>
            </p>
            ';
            if($i == 'g-recaptcha-score') continue;
            $messageVisitor .= '
            <p>
                ' . str_replace('_', ' ', $i) . ':<br />
                <strong>' . (trim($v) == ''?'-':$v) . '</strong>
            </p>
            ';
        }
        $bottomHtml = '';
        $bottomHtml .= '
                    </div>
        <!--[if mso]>
                </td>
                <td style="padding:0px;margin:0px;">&nbsp;</td>
            </tr>
        </table>
        <![endif]-->
        </body></html>
        ';

        $message1 = $topHtml . '<p>' . $introTextCompany . '</p>' . $messageCompany . $bottomHtml;
        $message2 = $topHtml . '<p>' . $introTextVisitor . '</p>' . $messageVisitor . $bottomHtml;

        return array($message1, $message2);
    }
    function writeToFile($file, $values) {
        $aLine = [];
        $aLine[] = date('Y-m-d H:i:s');
        if(trim(file_get_contents($file)) == '') file_put_contents($file, 'Tijdstip reservering;Naam;Bedrijfsnaam;E-mail adres;Telefoon;Aantal personen;Aanvullende wensen' . "\n");
        foreach($values as $i => $v) {
            if($i == '_token' || $i == 'g-recaptcha-response') continue;
            $v = Str::of($v)->trim();
            $v = Str::replace(';', ':', $v);
            // $v = Str::replace(array("\r", "\n"), '', $v);
            $v = trim(preg_replace('/\s\s+/', ' ', $v)); //https://stackoverflow.com/questions/3760816/remove-new-lines-from-string-and-replace-with-one-empty-space
            $aLine[] = trim($v);
        }
        file_put_contents($file, implode(';', $aLine) . "\n", FILE_APPEND);
    }
}