<?php
/**
 *
 *
 *
 */
class comment extends page
{
	var $db;
	var $auth;
	var $msg;
	var $title;
	var $status = 0; // 0=normal; 1=post

	/**
	 * Constructor
	 */
	function comment(&$db, &$auth, $msg)
	{
		$this->db = $db;
		$this->auth = $auth;
		$this->msg = $msg;
	}

	/**
	 *
	 */
	function process()
	{
		global $_SERVER;
		$is_post = ($_SERVER['REQUEST_METHOD'] == 'POST');
		if ($is_post)
		{
			$this->save_form();
			$this->status = 1;
		}
	}

	/**
	 *
	 */
	function show()
	{
		global $_GET;
		$ret .= sprintf('<h1>%1$s</h1>' . LF, $this->msg['comment']);
		if ($_GET['action'] == 'view')
			$ret .= $this->show_list();
		else
			$ret .= $this->show_form();
		$menu = $_GET['action'] == 'view' ? 'comment_entry' : 'comment_list';
		$action = $_GET['action'] == 'view' ? '' : '&action=view';
		$ret .= sprintf('<p><a href="%2$s">%1$s</a></p>' . LF,
			$this->msg[$menu],
			'./?mod=comment' . $action
		);

		return($ret);
	}


	/**
	 *
	 */
	function show_list()
	{
		$this->db->defaults['rperpage'] = 10;
		$cols = 'a.*';
		$from = 'FROM sys_comment a
			ORDER BY a.sent_date DESC';
		$rows = $this->db->get_rows_paged($cols, $from);
		if ($this->db->num_rows > 0)
		{
			$ret .= '<p>' . $this->db->get_page_nav() . '</p>' . LF;
			foreach ($rows as $row)
			{
				$ret .= sprintf(
					'<p><strong>%1$s</strong> (%2$s UTC)</p>' . LF,
					strip_tags($row['sender_name']),
					$row['sent_date']
				);
				$ret .= '<p>' . LF;
				$ret .= nl2br(strip_tags($row['comment_text'])) . LF;
				$ret .= '</p>' . LF;
				if ($row['response'])
				{
					$ret .= '<blockquote style="font-style:italic;">' . LF;
					$ret .= nl2br(strip_tags($row['response'])) . LF;
					$ret .= '</blockquote>' . LF;
				}
			}
			$ret .= '<p>' . $this->db->get_page_nav() . '</p>' . LF;
		}
		else
			$ret .= '<p>' . $this->msg['na'] . '</p>' . LF;

		return($ret);
	}

	/**
	 *
	 */
	function show_form()
	{
		$url_pattern = '/^http(s?):\/\/([\w-]+\.)+[\w-]+(\/[\w-\.\/\?%&=]*)?$/';
		$form = new form('entry_form', null, './?mod=comment');
		$form->setup($this->msg);

		$form->addElement('text', 'sender_name', $this->msg['comment_sender'], array('size' => 40, 'maxlength' => '255'));
		$form->addElement('text', 'sender_email', $this->msg['comment_email'], array('size' => 40, 'maxlength' => '255'));
		$form->addElement('text', 'url', $this->msg['url'], array('size' => 40, 'maxlength' => '255'));
		$form->addElement('textarea', 'comment_text', $this->msg['comment_text'], array('rows' => 10, 'style' => 'width: 100%'));
		$form->addElement('submit', 'save', $this->msg['submit']);
		$form->addRule('sender_name', sprintf($this->msg['required_alert'], $this->msg['comment_sender']), 'required', null, 'client');
		$form->addRule('sender_email', sprintf($this->msg['required_alert'], $this->msg['comment_email']), 'required', null, 'client');
		$form->addRule('sender_email', $this->msg['email_invalid'], 'email', null, 'client');
		$form->addRule('url', $this->msg['url_invalid'], 'regex', $url_pattern, 'client');
 		$form->addRule('comment_text', sprintf($this->msg['required_alert'], $this->msg['comment_text']), 'required', null, 'client');
		$form->setDefaults(array('url' => 'http://'));

		$msg = $this->msg[($this->status == 0 ? 'comment_welcome' : 'comment_sent')];
		$ret .= sprintf('<p>%1$s</p>' . LF, $msg);
		if ($this->status == 0) $ret .= $form->toHtml();
		return($ret);
	}

	/**
	 * Save data
	 */
	function save_form()
	{
		global $_POST;
		$query = sprintf('INSERT INTO sys_comment SET
			sender_name = %1$s,
			sender_email = %2$s,
			comment_text = %3$s,
			url = %4$s,
			ses_id = %5$s,
			sent_date = NOW();
			;',
			$this->db->quote($_POST['sender_name']),
			$this->db->quote($_POST['sender_email']),
			$this->db->quote($_POST['comment_text']),
			$this->db->quote($_POST['url']),
			$this->db->quote(session_id())
		);
		//die($query);
		$this->db->exec($query);
	}

};
?>