<?php 

return [
	/**
	 * Set to true to save the logs to the database
	 */
	'save-to-db'=>true,

	/**
	 * Maximum age for logs in the database, measured in days.  Set to 0 to never expire.
	 */
	'max-log-age'=>60,

	/**
	 * The SNS topic to report errors to
	 */
	'sns-topic'=>'arn:aws:sns:us-east-1:some-id:some-topic',

	/**
	 * The subject for the SNS topic
	 */
	'notification-subject'=>'Laravel Error',

	/**
	 * How long to cache an error message (and prevent the same one from being sent again)
	 * The cache key is the status code + the file + the line.
	 */
	'cache-hours'=>12,
];
