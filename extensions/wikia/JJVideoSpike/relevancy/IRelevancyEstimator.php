<?php
/**
 * User: artur
 * Date: 24.04.13
 * Time: 16:27
 */

interface IRelevancyEstimator {
	public function estimate( Article $article, array $metatags );
}
