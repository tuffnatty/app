<?php

/**
 * Class FsckVideoInfo
 *
 * Find discrepancies between the image table and the video_info table
 */
class FsckVideoInfo {
	const LOG_FILE = '/tmp/fsckVideoInfo';

	public static function run( DatabaseMysql $db, $test = false, $verbose = false, $params = null ) {
		$dbname = $params['dbname'];

		// Don't process the video wiki
		if ( $dbname == 'video151' ) {
			return true;
		}

		// Get all suggestion data in this wiki
		$sql = <<<SQL
select *
  from image left join video_info on img_name = video_title
 where img_major_mime = 'video';
SQL;

		if ( $verbose ) {
			echo "Running on $dbname\n";
		}

		if ( empty($test) ) {
			$res = $db->query($sql);

			// Loop through all the data
			while ($row = $db->fetchRow($res)) {

				// See if img_media_type and img_major_mime agree
				if (strtolower($row['img_media_type']) != strtolower($row['img_major_mime'])) {
					self::logMessage(
						'ERR_IMG_TYPE',
						$dbname,
						$row['img_media_type'].' != '.$row['img_major_mime'],
						$row['img_name']
					);
				}

				// See if there is a video_info record
				if ( empty( $row['video_title'] ) ) {
					self::logMessage(
						'ERR_NO_VIDEO_INFO',
						$dbname,
						'',
						$row['image_name']
					);
				}

				// See if the metadata agrees with video_info

				// The metadata field should be a serialized array
				$data = unserialize($row['img_metadata']);

				$videoId  = empty($data['videoId']) ? null : $data['videoId'];
				if ( $videoId != $row['video_id'] ) {
					self::logMessage(
						'ERR_MISMATCH_VIDEO_ID',
						$dbname,
						$videoId.' != '.$row['video_id'],
						$row['img_name']
					);
				}

				$provider = empty($data['provider']) ? $row['img_minor_mime'] : $data['provider'];
				if ( $provider != $row['provider'] ) {
					self::logMessage(
						'ERR_MISMATCH_PROVIDER',
						$dbname,
						$provider.' != '.$row['provider'],
						$row['img_name']
					);
				}

				$duration = empty($data['duration']) ? 0 : $data['duration'];
				if ( $duration != $row['duration'] ) {
					self::logMessage(
						'ERR_MISMATCH_DURATION',
						$dbname,
						$duration.' != '.$row['duration'],
						$row['img_name']
					);
				}
			}
		}
	}

	public static function logMessage( $err, $dbname, $note, $file ) {
		$msg = "[$err] $dbname : ($note) $file\n";

		file_put_contents( self::LOG_FILE, $msg, FILE_APPEND );
	}
}
