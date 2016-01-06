<?php
/**
 * @author     Daniel Dimitrov <daniel@compojoom.com>
 * @date       22.02.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 *
 * This is a modified version of the JMailer Class that works with
 * the Mandrill API
 */

/**
 * @version        $Id: mail.php 14401 2010-01-26 14:10:00Z louis $
 * @package        Joomla.Framework
 * @subpackage     Mail
 * @copyright      Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license        GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport('phpmailer.phpmailer');
jimport('joomla.mail.helper');

require_once JPATH_LIBRARIES . '/cmandrill/include.php';

JLoader::discover('CmandrillHelper', JPATH_ADMINISTRATOR . '/components/com_cmandrill/helpers/');

/**
 * Email Class.  Provides a common interface to send email from the Joomla! Platform
 *
 * @package     Joomla.Platform
 * @subpackage  Mail
 * @since       11.1
 */
class JMail extends PHPMailer
{
	/**
	 * @var    array  JMail instances container.
	 * @since  11.3
	 */
	protected static $instances = array();

	/**
	 * @var    string  Charset of the message.
	 * @since  11.1
	 */
	public $CharSet = 'utf-8';

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Phpmailer has an issue using the relative path for it's language files
		$this->SetLanguage('joomla', JPATH_PLATFORM . '/joomla/mail/language/');

		// Load the admin language
		$language = JFactory::getLanguage();
		$language->load('plg_system_mandrill.sys', JPATH_ADMINISTRATOR, 'en-GB', true);
		$language->load('plg_system_mandrill.sys', JPATH_ADMINISTRATOR, $language->getDefault(), true);
		$language->load('plg_system_mandrill.sys', JPATH_ADMINISTRATOR, null, true);

		$this->mandrill = CmandrillHelperMandrill::initMandrill(false);

		// Initialize the logger class
		jimport('joomla.error.log');
		$date = JFactory::getDate()->format('Y_m');

		// Add the logger.
		JLog::addLogger(
			array(
				'text_file' => 'plg_system_mandrill.log.' . $date . '.php'
			)

		);
	}

	/**
	 * Returns the global email object, only creating it
	 * if it doesn't already exist.
	 *
	 * NOTE: If you need an instance to use that does not have the global configuration
	 * values, use an id string that is not 'Joomla'.
	 *
	 * @param   string  $id  The id string for the JMail instance [optional]
	 *
	 * @return  JMail  The global JMail object
	 *
	 * @since   11.1
	 */
	public static function getInstance($id = 'Joomla')
	{
		if (empty(self::$instances[$id]))
		{
			self::$instances[$id] = new JMail;
		}

		return self::$instances[$id];
	}

	/**
	 * Sends the email -> either trough PHPMailer or through Mandrill
	 *
	 * @return mixed True if successful, a JError object otherwise
	 */
	public function Send()
	{
		if (!$this->isDailyQuotaExeeded())
		{
			return $this->mandrillSend();
		}

		return $this->phpMailerSend();
	}

	/**
	 * Send the mail with phpMailer
	 *
	 * @return  mixed  True if successful; JError if using legacy tree (no exception thrown in that case).
	 *
	 * @since   11.1
	 * @throws  RuntimeException
	 */
	public function phpMailerSend()
	{
		if (JFactory::getConfig()->get('mailonline', 1))
		{
			if (($this->Mailer == 'mail') && !function_exists('mail'))
			{
				throw new RuntimeException(sprintf('%s::Send mail not enabled.', get_class($this)));
			}

			$result = parent::send();

			if ($result == false)
			{
				throw new RuntimeException(sprintf('%s::Send failed: "%s".', get_class($this), $this->ErrorInfo));
			}

			return $result;
		}
		else
		{
			JFactory::getApplication()->enqueueMessage(JText::_('JLIB_MAIL_FUNCTION_OFFLINE'));

			return false;
		}
	}

	/**
	 * Set the email sender
	 *
	 * @param   array  $from  email address and Name of sender
	 *                        <code>array([0] => email Address [1] => Name)</code>
	 *
	 * @return  JMail  Returns this object for chaining.
	 *
	 * @since   11.1
	 */
	public function setSender($from)
	{
		if (is_array($from))
		{
			// If $from is an array we assume it has an address and a name
			if (isset($from[2]))
			{
				// If it is an array with entries, use them
				$this->setFrom(JMailHelper::cleanLine($from[0]), JMailHelper::cleanLine($from[1]), (bool) $from[2]);
			}
			else
			{
				$this->setFrom(JMailHelper::cleanLine($from[0]), JMailHelper::cleanLine($from[1]));
			}
		}
		elseif (is_string($from))
		{
			// If it is a string we assume it is just the address
			$this->setFrom(JMailHelper::cleanLine($from));
		}
		else
		{
			// If it is neither, we log a message and throw an exception
			JLog::add(JText::sprintf('JLIB_MAIL_INVALID_EMAIL_SENDER', $from), JLog::WARNING, 'jerror');

			throw new UnexpectedValueException(sprintf('Invalid email Sender: %s, JMail::setSender(%s)', $from));
		}

		return $this;
	}

	/**
	 * Set the email subject
	 *
	 * @param   string  $subject  Subject of the email
	 *
	 * @return  JMail  Returns this object for chaining.
	 *
	 * @since   11.1
	 */
	public function setSubject($subject)
	{
		$this->Subject = JMailHelper::cleanLine($subject);

		return $this;
	}

	/**
	 * Set the email body
	 *
	 * @param   string  $content  Body of the email
	 *
	 * @return  JMail  Returns this object for chaining.
	 *
	 * @since   11.1
	 */
	public function setBody($content)
	{
		/*
		 * Filter the Body
		 * TODO: Check for XSS
		 */
		$this->Body = JMailHelper::cleanText($content);

		return $this;
	}

	/**
	 * Add recipients to the email.
	 *
	 * @param   mixed   $recipient  Either a string or array of strings [email address(es)]
	 * @param   mixed   $name       Either a string or array of strings [name(s)]
	 * @param   string  $method     The parent method's name.
	 *
	 * @return  JMail  Returns this object for chaining.
	 *
	 * @since   11.1
	 * @throws  InvalidArgumentException
	 */
	protected function add($recipient, $name = '', $method = 'addAddress')
	{
		$method = lcfirst($method);

		// If the recipient is an array, add each recipient... otherwise just add the one
		if (is_array($recipient))
		{
			if (is_array($name))
			{
				$combined = array_combine($recipient, $name);

				if ($combined === false)
				{
					throw new InvalidArgumentException("The number of elements for each array isn't equal.");
				}

				foreach ($combined as $recipientEmail => $recipientName)
				{
					$recipientEmail = JMailHelper::cleanLine($recipientEmail);
					$recipientName = JMailHelper::cleanLine($recipientName);
					call_user_func('parent::' . $method, $recipientEmail, $recipientName);
				}
			}
			else
			{
				$name = JMailHelper::cleanLine($name);

				foreach ($recipient as $to)
				{
					$to = JMailHelper::cleanLine($to);
					call_user_func('parent::' . $method, $to, $name);
				}
			}
		}
		else
		{
			$recipient = JMailHelper::cleanLine($recipient);
			call_user_func('parent::' . $method, $recipient, $name);
		}

		return $this;
	}

	/**
	 * Add recipients to the email
	 *
	 * @param   mixed  $recipient  Either a string or array of strings [email address(es)]
	 * @param   mixed  $name       Either a string or array of strings [name(s)]
	 *
	 * @return  JMail  Returns this object for chaining.
	 *
	 * @since   11.1
	 */
	public function addRecipient($recipient, $name = '')
	{
		$this->add($recipient, $name, 'addAddress');

		return $this;
	}

	/**
	 * Add carbon copy recipients to the email
	 *
	 * @param   mixed  $cc    Either a string or array of strings [email address(es)]
	 * @param   mixed  $name  Either a string or array of strings [name(s)]
	 *
	 * @return  JMail  Returns this object for chaining.
	 *
	 * @since   11.1
	 */
	public function addCC($cc, $name = '')
	{
		// If the carbon copy recipient is an array, add each recipient... otherwise just add the one
		if (isset($cc))
		{
			$this->add($cc, $name, 'addCC');
		}

		return $this;
	}

	/**
	 * Add blind carbon copy recipients to the email
	 *
	 * @param   mixed  $bcc   Either a string or array of strings [email address(es)]
	 * @param   mixed  $name  Either a string or array of strings [name(s)]
	 *
	 * @return  JMail  Returns this object for chaining.
	 *
	 * @since   11.1
	 */
	public function addBCC($bcc, $name = '')
	{
		// If the blind carbon copy recipient is an array, add each recipient... otherwise just add the one
		if (isset($bcc))
		{
			$this->add($bcc, $name, 'addBCC');
		}

		return $this;
	}

	/**
	 * Add file attachment to the email
	 *
	 * @param   mixed   $path         Either a string or array of strings [filenames]
	 * @param   mixed   $name         Either a string or array of strings [names]
	 * @param   mixed   $encoding     The encoding of the attachment
	 * @param   mixed   $type         The mime type
	 * @param   string  $disposition  The disposition of the attachment
	 *
	 * @return  JMail  Returns this object for chaining.
	 *
	 * @since   12.2
	 * @throws  InvalidArgumentException
	 */
	public function addAttachment($path, $name = '', $encoding = 'base64', $type = 'application/octet-stream', $disposition = 'attachment')
	{
		// If the file attachments is an array, add each file... otherwise just add the one
		if (isset($path))
		{
			if (is_array($path))
			{
				if (!empty($name) && count($path) != count($name))
				{
					throw new InvalidArgumentException("The number of attachments must be equal with the number of name");
				}

				foreach ($path as $key => $file)
				{
					if (!empty($name))
					{
						parent::addAttachment($file, $name[$key], $encoding, $type);
					}
					else
					{
						parent::addAttachment($file, $name, $encoding, $type);
					}
				}
			}
			else
			{
				parent::addAttachment($path, $name, $encoding, $type);
			}
		}

		return $this;
	}

	/**
	 * Add Reply to email address(es) to the email
	 *
	 * @param   array         $replyto  Either an array or multi-array of form
	 *                                  <code>array([0] => email Address [1] => Name)</code>
	 * @param   array|string  $name     Either an array or single string
	 *
	 * @return  JMail  Returns this object for chaining.
	 *
	 * @since   11.1
	 */
	public function addReplyTo($replyto, $name = '')
	{
		$this->add($replyto, $name, 'addReplyTo');

		return $this;
	}

	/**
	 * Use sendmail for sending the email
	 *
	 * @param   string  $sendmail  Path to sendmail [optional]
	 *
	 * @return  boolean  True on success
	 *
	 * @since   11.1
	 */
	public function useSendmail($sendmail = null)
	{
		$this->Sendmail = $sendmail;

		if (!empty($this->Sendmail))
		{
			$this->IsSendmail();

			return true;
		}
		else
		{
			$this->IsMail();

			return false;
		}
	}

	/**
	 * Use SMTP for sending the email
	 *
	 * @param   string   $auth    SMTP Authentication [optional]
	 * @param   string   $host    SMTP Host [optional]
	 * @param   string   $user    SMTP Username [optional]
	 * @param   string   $pass    SMTP Password [optional]
	 * @param   string   $secure  Use secure methods
	 * @param   integer  $port    The SMTP port
	 *
	 * @return  boolean  True on success
	 *
	 * @since   11.1
	 */
	public function useSMTP($auth = null, $host = null, $user = null, $pass = null, $secure = null, $port = 25)
	{
		$this->SMTPAuth = $auth;
		$this->Host = $host;
		$this->Username = $user;
		$this->Password = $pass;
		$this->Port = $port;

		if ($secure == 'ssl' || $secure == 'tls')
		{
			$this->SMTPSecure = $secure;
		}

		if (($this->SMTPAuth !== null && $this->Host !== null && $this->Username !== null && $this->Password !== null)
			|| ($this->SMTPAuth === null && $this->Host !== null))
		{
			$this->IsSMTP();

			return true;
		}
		else
		{
			$this->IsMail();

			return false;
		}
	}

	/**
	 * Function to send an email
	 *
	 * @param   string    $from         From email address
	 * @param   string    $fromName     From name
	 * @param   mixed     $recipient    Recipient email address(es)
	 * @param   string    $subject      email subject
	 * @param   string    $body         Message body
	 * @param   bool|int  $mode         false = plain text, true = HTML
	 * @param   mixed     $cc           CC email address(es)
	 * @param   mixed     $bcc          BCC email address(es)
	 * @param   mixed     $attachment   Attachment file name(s)
	 * @param   mixed     $replyTo      Reply to email address(es)
	 * @param   mixed     $replyToName  Reply to name(s)
	 *
	 * @return  boolean  True on success
	 *
	 * @since   11.1
	 */
	public function sendMail($from, $fromName, $recipient, $subject, $body, $mode = false, $cc = null, $bcc = null, $attachment = null,
		$replyTo = null, $replyToName = null)
	{
		$this->setSubject($subject);
		$this->setBody($body);

		// Are we sending the email as HTML?
		if ($mode)
		{
			$this->IsHTML(true);
		}

		$this->addRecipient($recipient);
		$this->addCC($cc);
		$this->addBCC($bcc);
		$this->addAttachment($attachment);

		// Take care of reply email addresses
		if (is_array($replyTo))
		{
			$numReplyTo = count($replyTo);

			for ($i = 0; $i < $numReplyTo; $i++)
			{
				$this->addReplyTo($replyTo[$i], $replyToName[$i]);
			}
		}
		elseif (isset($replyTo))
		{
			$this->addReplyTo($replyTo, $replyToName);
		}

		// Add sender to replyTo only if no replyTo received
		$autoReplyTo = (empty($this->ReplyTo)) ? true : false;
		$this->setSender(array($from, $fromName, $autoReplyTo));

		return $this->Send();
	}

	/**
	 * Sends mail to administrator for approval of a user submission
	 *
	 * @param   string  $adminName   Name of administrator
	 * @param   string  $adminEmail  Email address of administrator
	 * @param   string  $email       [NOT USED TODO: Deprecate?]
	 * @param   string  $type        Type of item to approve
	 * @param   string  $title       Title of item to approve
	 * @param   string  $author      Author of item to approve
	 * @param   string  $url         A URL to included in the mail
	 *
	 * @return  boolean  True on success
	 *
	 * @since   11.1
	 */
	public function sendAdminMail($adminName, $adminEmail, $email, $type, $title, $author, $url = null)
	{
		$subject = JText::sprintf('JLIB_MAIL_USER_SUBMITTED', $type);

		$message = sprintf(JText::_('JLIB_MAIL_MSG_ADMIN'), $adminName, $type, $title, $author, $url, $url, 'administrator', $type);
		$message .= JText::_('JLIB_MAIL_MSG') . "\n";

		$this->addRecipient($adminEmail);
		$this->setSubject($subject);
		$this->setBody($message);

		return $this->Send();
	}

	/**
	 * Check the daily quota limit
	 *
	 * @return bool
	 */
	private function isDailyQuotaExeeded()
	{
		$data = $this->mandrill->users->info();

		$dailyQuota = $data->hourly_quota * 24;

		$sentToday = $data->stats->today->sent;

		if ((int) $dailyQuota <= (int) $sentToday)
		{
			$this->writeToLog(JText::sprintf('PLG_SYSTEM_MANDRILL_DAILY_QUOTA_EXCEEDED', (int) $dailyQuota, (int) $sentToday));

			return true;
		}

		return false;
	}

	/**
	 * Send the mail through the Mandrill API
	 *
	 * @return bool|mixed
	 */
	private function mandrillSend()
	{
		$to = array();
		$config = JComponentHelper::getParams('com_cmandrill');
		$attachments = $this->GetAttachments();
		$nAttachments = array();
		$iAttachments = array();

		if (count($attachments) > 0)
		{
			foreach ($attachments as $attachment)
			{
				if ($attachment[6] == 'inline')
				{
					// Inline attachment (normally image)
					$iAttachments[] = array(
						'name' => $attachment[7],
						'type' => $this->filenameToType($attachment[1]),
						'content' => $this->EncodeFile($attachment[0])
					);
				}
				else
				{
					// Normal attachment
					$nAttachments[] = array(
						'name' => $attachment[2],
						'type' => $this->filenameToType($attachment[1]),
						'content' => $this->EncodeFile($attachment[0])
					);
				}
			}
		}

		$message = array(
			'subject' => $this->Subject,
			'from_email' => $this->From,
			'from_name' => $this->FromName
		);

		if ($config->get('subaccount', ''))
		{
			$message["subaccount"] = $config->get('subaccount', '');
		}

		if (count($nAttachments))
		{
			$message['attachments'] = $nAttachments;
		}

		if (count($iAttachments))
		{
			$message['images'] = $iAttachments;
		}

		$who = CmandrillHelperMandrill::whoIsSendingIt();

		if (isset($who['class']))
		{
			$message['tags'][] = 'class_' . $who['class'];
		}

		if (isset($who['function']))
		{
			$message['tags'][] = 'function_' . $who['function'];
		}


		if (count($this->ReplyTo) > 0)
		{
			$replyTo = array_keys($this->ReplyTo);
			$message['headers'] = array('Reply-To' => $replyTo[0]);
		}

		if ($this->ContentType == 'text/plain')
		{
			$message['text'] = $this->Body;
		}
		else
		{
			$message['html'] = $this->Body;
			$message['auto_text'] = true;
		}

		$message['track_opens'] = true;
		$message['track_clicks'] = true;

		// Add the to
		foreach ($this->to as $value)
		{
			$to[] = array(
				'email' => $value[0],
				'name' => $value[1],
				'type' => 'to'
			);
		}

		// Add the cc
		foreach ($this->cc as $value)
		{
			$to[] = array(
				'email' => $value[0],
				'name' => $value[1],
				'type' => 'cc'
			);
		}

		// Add the bcc
		foreach ($this->bcc as $value)
		{
			$to[] = array(
				'email' => $value[0],
				'name' => $value[1],
				'type' => 'bcc'
			);
		}

		// If we have a template, then use it!
		$templateName = cmandrillHelperMandrill::getTemplate();

		if ($templateName)
		{
			$html = $this->Body;

			// If we have a template, we need to send the mail in HTML format
			// so if joomla is sending it in text/plain, then we need to make some modifications to it
			if ($this->ContentType == 'text/plain')
			{
				$html = nl2br(htmlspecialchars($html));

				// Replace multiple spaces with single spaces
				$html = preg_replace('/\s\s+/', ' ', $html);

				// Replace URLs with <a href...> elements
				$html = cmandrillHelperUtility::makeClickableUrls($html);
			}

			$templateContent = array(
				array(
					'name' => 'main_content',
					'content' => $html
				)
			);
		}

		// If we have more than 1000 recipients, let us send this in chunks
		$to = array_chunk($to, 1000);
		$status = array();

		foreach ($to as $value)
		{
			$message['to'] = $value;

			if ($templateName)
			{
				$data = $this->mandrill->messages->sendTemplate($templateName, $templateContent, $message, false);
			}
			else
			{
				$data = $this->mandrill->messages->send($message, false);
			}

			// Check if we have have a correct response
			if (is_array($data))
			{
				foreach ($data as $user)
				{
					$status[$user->status][] = $user;
				}
			}
		}

		// Queued mails??? Strange beast with mandrill. Most probably the mail was sent, we log this and treat it as sent
		if (isset($status['queued']) && count($status['queued']))
		{
			foreach ($status['queued'] as $value)
			{
				$queuedMessage[] = $value->email;
			}

			$this->writeToLog(JText::sprintf('PLG_MANDRILL_EMAIL_TO_QUEUED', implode(',', $queuedMessage)));

			return true;
		}

		/**
		 * If we have rejected emails - try to send them with phpMailer
		 * not a perfect solution because we will return the result form phpMailer instead of the Mandrill
		 * but better to try to deliver again than to fail to send the message
		 */
		if (isset($status['rejected']) && count($status['rejected']))
		{
			// Clear the addresses
			$this->ClearAddresses();

			// Go over each rejected address, add it to the log and then add it to the PHPMailer class
			foreach ($status['rejected'] as $rejected)
			{
				$this->writeToLog(JText::sprintf('PLG_MANDRILL_EMAIL_TO_REJECTED', $rejected->email, $rejected->reject_reason));
				$this->addRecipient($rejected->email);
			}

			// Now try to send with PhpMailer
			return $this->phpMailerSend();
		}

		// Iet us hope that we always come so far!
		if (isset($status['sent']) && count($status['sent']))
		{
			return true;
		}

		return false;
	}

	/**
	 * Writes to the log
	 *
	 * @param   string  $message  - the message
	 *
	 * @return void
	 */
	private function writeToLog($message)
	{
		JLog::add($message, JLog::WARNING, 'mandrill');
	}
}
