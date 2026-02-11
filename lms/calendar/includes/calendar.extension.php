<?php
	
	class AFFC5Calendar_Extensions
	{
		/**
		* This function converts date from one timezone to another
		*/
		protected function convertDateFromTimezone($date, $timezone, $timezone_to, $format)
		{
			if($timezone == '')
			{
				$timezone = date_default_timezone_get();
			}
			
			$date = new DateTime($date, new DateTimeZone($timezone));
			$date->setTimezone( new DateTimeZone($timezone_to) );
			return $date->format($format);
		}

		/**
		* This function updates event drag, resize
		*/
		protected function update_repetitive_event_drag_resize($start, $end, $allDay_value, $id)
		{
			$the_query = "allDay = '$allDay_value'";	
			
			$query = sprintf("UPDATE %s 
									SET 
										start = '%s',
										end = '%s',
										%s
									WHERE
										id = '%d'
						",
										$this->db_escape($this->table),
										$this->db_escape($start),
										$this->db_escape($end),
										$the_query,
										$this->db_escape($id)
						);

			// The result
			return $this->result = mysqli_query($this->connection, $query);
		}
		
		/**
		* This function extends the update functions (all repetitives event procedure for updates)
		* This function will update all repetitive events data without the times.
		*/
		protected function update_all_repetitive_event($allDay, $start, $end, $id, $repeat_id, $extra)
		{
			if(isset($extra['url']))
			{
				$url = $this->db_escape($extra['url']);
			} else {
				$url = "false";	
			}
			
			$title = $this->db_escape($extra['title']);
			$description = $this->db_escape($extra['description']);
			$color = $this->db_escape($extra['color']);
			$category = $this->db_escape($extra['category']);
			
			$query = sprintf("UPDATE %s 
									SET 
										title = '%s', 
										description = '%s', 
										color = '%s', 
										category = '%s', 
										url = '%s'
									WHERE
										repeat_id = '%d'
						",
										$this->db_escape($this->table),
										$this->db_escape($title),
										$this->db_escape($description),
										$this->db_escape($color),
										$this->db_escape($category),
										$this->db_escape($url),
										$this->db_escape($repeat_id)
						);
			
			// The result
			return $this->result = mysqli_query($this->connection, $query);
		}
		
		/**
		* Insert Repetitive Events Query (since 1.6.4)
		*/
		protected function insert_repetitive_query($fields, $start, $end)
		{
			$query =  mysqli_query($this->connection, sprintf("INSERT INTO %s 
															SET 
																title = '%s',
																description = '%s',
																start = '%s',
																end = '%s',
																allDay = '%s',
																color = '%s',
																url = '%s',
																category = '%s',
																user_id = '%d',
																repeat_id = '%d',
																repeat_type = '%s',
																timezone = '%s'
												",
													$fields['table'],
													$fields['title'],
													$fields['description'],
													$start,
													$end,
													$fields['all-day'],
													$fields['color'],
													$fields['url'],
													$fields['categorie'],
													$fields['user_id'],
													$fields['repeat_id'],
													$fields['repeat_method'],
													$fields['timezone']
												));
			
			$inserted_id = mysqli_insert_id($this->connection);
			
			unset($fields['title'], $fields['description']);
			unset($fields['start_date'], $fields['start_time'], $fields['end_date'], $fields['end_time']);
			unset($fields['all-day'], $fields['color'], $fields['url'], $fields['categorie']);
			unset($fields['user_id']);
			unset($fields['repeat_method'], $fields['repeat_times'], $fields['repeat_id']);
				
			// add all other custom fields
			if(!empty($fields))
			{
				foreach($fields as $k => $v)
				{ 
					$fk = $this->db_escape($k);
					mysqli_query(
						$this->connection, 
						sprintf("UPDATE %s SET `{$fk}` = '%s' WHERE id = '%d'", $this->db_escape($this->table), $this->db_escape($v), $inserted_id)
					);
				}
			}
			
		}
		
		/**
		* Insert Repetitive Events (since 1.6.4)
		*/
		protected function insert_repetitive_events($fields, $current_date, $current_month, $current_year)
		{
			$repeat_times = $fields['repeat_times'];
			
			$end_current_date = date('d', strtotime($fields['end_date']));
			$end_current_month = date('m', strtotime($fields['end_date']));
			$end_current_year = date('Y', strtotime($fields['end_date']));
			
			switch($fields['repeat_method'])
			{
				case 'every_day':
					if($repeat_times <= '30')
					{
						for($i = 1; $i <= $repeat_times; $i++)
						{
							$start = date('Y-m-d', strtotime("+$i day", strtotime($current_year.'-'.$current_month.'-'.$current_date))) . ' ' .$fields['start_time'].':00';
							$end = date('Y-m-d', strtotime("+$i day", strtotime($end_current_year.'-'.$end_current_month.'-'.$end_current_date))) . ' ' .$fields['end_time'].':00';
							$this->insert_repetitive_query($fields, $start, $end);
						}
						return true;
					}
				break;
				
				case 'every_week':
					if($repeat_times <= 30)
					{
						for($i = 1; $i <= $repeat_times; $i++)
						{
							$start = date('Y-m-d', strtotime("+$i week", strtotime($current_year.'-'.$current_month.'-'.$current_date))) . ' ' .$fields['start_time'].':00';
							$end = date('Y-m-d', strtotime("+$i week", strtotime($end_current_year.'-'.$end_current_month.'-'.$end_current_date))) . ' ' .$fields['end_time'].':00';
							$this->insert_repetitive_query($fields, $start, $end);
						}
						return true;
					}
				break;
				
				case 'every_month':
					if($repeat_times <= 30)
					{
						for($i = 1; $i <= $repeat_times; $i++)
						{
							$start = date('Y-m-d', strtotime("+$i month", strtotime($current_year.'-'.$current_month.'-'.$current_date))) . ' ' .$fields['start_time'].':00';
							$end = date('Y-m-d', strtotime("+$i month", strtotime($end_current_year.'-'.$end_current_month.'-'.$end_current_date))) . ' ' .$fields['end_time'].':00';
							$this->insert_repetitive_query($fields, $start, $end);
						}
						return true;
					}
				break;
				
				case 'every_year':
					if($repeat_times <= 30)
					{
						for($i = 1; $i <= $repeat_times; $i++)
						{
							$start = date('Y-m-d', strtotime("+$i year", strtotime($current_year.'-'.$current_month.'-'.$current_date))) . ' ' .$fields['start_time'].':00';
							$end = date('Y-m-d', strtotime("+$i year", strtotime($end_current_year.'-'.$end_current_month.'-'.$end_current_date))) . ' ' .$fields['end_time'].':00';
							$this->insert_repetitive_query($fields, $start, $end);
						}
						return true;
					}
				break;
			}
		}
		
		/**
		* This function updates custom fields
		*/
		protected function update_custom_fields($post, $id) 
		{
			if(!empty($post))
			{
				foreach($post as $k => $v)
				{
					$fk = $this->db_escape($k);					
					mysqli_query(
						$this->connection, 
						sprintf("UPDATE %s SET `{$fk}` = '%s' WHERE id = '%d'", $this->db_escape($this->table), $this->db_escape($v), $id)
					);
				}
			}
		}
		
		/**
		* This function handles file upload
		*/
		protected function handle_file_upload($post, $file) 
		{
			if(!empty($file))
			{
				if(strlen($file['file']['name']) !== 0)
				{
					// Allowed Extensions
					$targetFolder = $this->uploadDir;
					$fileTypes = array('zip','pdf','doc','ppt','xls','txt','docx','xlsx','pptx','png','jpg','gif');
					$fileParts = pathinfo($file['file']['name']);

					$tempFile = $file['file']['tmp_name'];

					$timestamp = time();
					$filename = $timestamp . $_FILES['file']['name'];
					$targetFile = $targetFolder . $filename;
					
					if(in_array($fileParts['extension'], $fileTypes)) 
					{
						$upd = move_uploaded_file($tempFile, $targetFile);
						if($upd) 
						{
							$post['file'] = SITE_FILES_URL.$filename;
							return $post;
						} else {
							$post['file'] = '';
							return $post;
						}
						
					} else {
						return $post;
					}
				} else {
					return $post;
				}
			} else {
				return $post;
			}
		}
		
		/**
		* Strip unwanted tags from the calendar
		* Those that want HTML support on the calendar use this function on the 'updates' and 'addEvent' to the $description
		* like this $this->strip_html_tags($description) to filter it and use on the function 'json_transform' htmlspecialchars_decode($event_description)
		* to render html to the event description.
		*/
		protected function strip_html_tags($text)
		{
			$text = preg_replace('~<\s*\bscript\b[^>]*>(.*?)<\s*\/\s*script\s*>~is', '', $text);
			$text = preg_replace('~<\s*\bhead\b[^>]*>(.*?)<\s*\/\s*head\s*>~is', '', $text);
			$text = preg_replace('~<\s*\bstyle\b[^>]*>(.*?)<\s*\/\s*style\s*>~is', '', $text);
			$text = preg_replace('~<\s*\bobject\b[^>]*>(.*?)<\s*\/\s*object\s*>~is', '', $text);
			$text = preg_replace('~<\s*\bapplet\b[^>]*>(.*?)<\s*\/\s*applet\s*>~is', '', $text);
			$text = preg_replace('~<\s*\bnoframes\b[^>]*>(.*?)<\s*\/\s*noframes\s*>~is', '', $text);
			$text = preg_replace('~<\s*\bnoscript\b[^>]*>(.*?)<\s*\/\s*noscript\s*>~is', '', $text);
			$text = preg_replace('~<\s*\bframeset\b[^>]*>(.*?)<\s*\/\s*frameset\s*>~is', '', $text);
			$text = preg_replace('~<\s*\bframe\b[^>]*>(.*?)<\s*\/\s*frame\s*>~is', '', $text);
			$text = preg_replace('~<\s*\biframe\b[^>]*>(.*?)<\s*\/\s*iframe\s*>~is', '', $text);
			$text = preg_replace('~<\s*\bform\b[^>]*>(.*?)<\s*\/\s*form\s*>~is', '', $text);
			$text = preg_replace('/on[a-z]+=\".*\"/i', '', $text);
			
			return $text;
			
		}
				
	}

?>