<?php
class WikiaRuns implements iXHProfRuns {
	const MYSQL_HOST = 'localhost';
	const MYSQL_USER = 'root';
	const MYSQL_DB = 'dart_profiler';

	/** @var PDO */
	private static $pdo;

	public function __construct() {
		if (self::$pdo == null) {
			self::$pdo = new PDO("mysql:dbname=".self::MYSQL_DB.";host=".self::MYSQL_HOST, self::MYSQL_USER);
		}
	}

	public function getSlowestRequests($limit=10) {
		$sql = "
			SELECT *
			FROM `request`
			ORDER BY `total_time` DESC
			LIMIT {$limit}
		";

		$requests = "
			<style>
			td, th {
				border: 1px solid black;
				padding: 5px;
			}

			th {
				text-align: left;
			}
			</style>
			Slowest Runs <hr />
			<table>
				<tr>
					<th>Link</th>
					<th>Total Time (s)</th>
					<th>Entry Point</th>
					<th>URL</th>
				</tr>
		";
		$query = self::$pdo->query($sql);
		$script = htmlentities($_SERVER['SCRIPT_NAME']);
		while ($req = $query->fetchObject()) {
			$requests .= "
				<tr>
					<td><a href='{$script}?run={$req->id}&source=unset'>Link</a></td>
					<td>{$req->total_time}</td>
					<td>{$req->entry_point}</td>
					<td>{$req->url}</td>
				</tr>
			";
		}

		return $requests."</table>";
	}

	public function get_run($runId, $type, &$runDesc) {
		$sql = "
			SELECT *
			FROM `details`
			WHERE `request_id`={$runId}
		";

		$runData = [];
		$query = self::$pdo->query($sql);
		while ($row = $query->fetchObject()) {
			$key = isset($row->called_from) ? "{$row->called_from}==>{$row->fname}" : $row->fname;
			$runData[$key] = [
				'ct' => $row->ct,
				'pmu' => $row->pmu,
				'wt' => $row->wt,
				'cpu' => $row->cpu,
			];
		}

		return $runData;
	}

	public function save_run($data, $type, $runId=null) {

	}

	public function list_runs() {
		$sql = "
			SELECT *
			FROM `request`
			ORDER BY `timestamp` DESC
		";

		$runs = "Existing Runs<hr/><ul>";
		$query = self::$pdo->query($sql);
		$script = htmlentities($_SERVER['SCRIPT_NAME']);
		while ($row = $query->fetchObject()) {
			$runs .= "
				<li>
					<a href='{$script}?run={$row->id}&source=unset'>{$row->url} ({$row->timestamp})</a>
				</li>
			";
		}

		echo $runs."</ul>";
	}

	public static function getRequestData($requestId) {
		$sql = "
			SELECT *
			FROM `request`
			WHERE `id`={$requestId}
			LIMIT 1
		";

		$query = self::$pdo->query($sql);
		return $query->fetchObject();
	}
}