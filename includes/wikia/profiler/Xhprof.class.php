<?php
/**
 * ProfilerXhprof
 *
 * <insert description here>
 *
 * @author Nelson Monterroso <nelson@wikia-inc.com>
 */

namespace Wikia\profiler;


class Xhprof extends \ProfilerStub {
	const DEFAULT_TIMEOUT = 20;

	private static $ignoredFunctions = [
		'AutoLoader::autoload',
		'wfProfileIn',
		'wfProfileOut',
		'Profiler::instance',
		'ProfilerStub::profileIn',
		'ProfilerStub::profileOut',
		'Wikia\profiler\Xhprof::onShutdown',
	];

	private $startTime;
	private $sampleRate;
	private $timeout;

	public function __construct($sampleRate, $timeout=self::DEFAULT_TIMEOUT) {
		$sampleRate = 100;
		$this->startTime = microtime(true);
		$this->sampleRate = $sampleRate;
		$this->timeout = $timeout;

		register_shutdown_function([$this, 'onShutdown']);
		xhprof_enable(XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY | XHPROF_FLAGS_NO_BUILTINS, [
			'ignored_functions' => self::$ignoredFunctions,
		]);
	}

	public function onShutdown() {
		$totalTime = microtime(true) - $this->startTime;
		$record = mt_rand(1, 100) <= $this->sampleRate || $totalTime < $this->timeout;

		if (!$record) {
			return;
		}

		$data = xhprof_disable();

		$json = json_encode($data);
		$serialized = serialize($data);

		file_put_contents("/home/nelson/xhprof/runs/".microtime(true).".xhprof", $serialized);
	}
}