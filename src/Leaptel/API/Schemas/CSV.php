<?php

namespace Leaptel\API\Schemas;

use Symfony\Component\HttpFoundation\StreamedResponse;

class CSV
{
	private $collection;
	private array $cols = [];
	private string $footer = "";

	/**
	 * @param mixed $collection
	 * @param array $exportcols
	 * @return void
	 * @throws \Exception
	 */
	public function __construct($collection, array $exportcols = [])
	{
		if (!is_iterable($collection)) {
			throw new \Exception("Can't iterate over collection provided");
		}
		$this->collection = $collection;
		$this->cols = $exportcols;
	}

	public function setFooter(string $footer): self
	{
		$this->footer = $footer;
		return $this;
	}

	/**
	 * Mark all cols as exported. Optional array of cols to exclude
	 *
	 * @param array $excluding
	 * @return \Leaptel\API\Schemas\CSV
	 */
	public function exportAllCols(array $excluding = []): self
	{
		$cols =  [];
		$first = reset($this->collection);
		foreach ($first as $k => $v) {
			$cols[$k] = $k;
		}
		foreach ($excluding as $k) {
			unset($cols[$k]);
		}
		$this->cols = $cols;
		return $this;
	}

	/**
	 * Rename a header from->to (also ADDs a header if needed). This only changes the
	 * string on the first line
	 *
	 * @param string $from
	 * @param string $to
	 * @return \Leaptel\API\Schemas\CSV
	 */
	public function aliasHeader(string $from, string $to): self
	{
		$this->cols[$from] = $to;
		return $this;
	}

	/** @return array */
	private function getHeader(): array
	{
		return $this->cols;
	}

	/**
	 * Simple export of result
	 *
	 * @param boolean $withheader
	 * @return array
	 * @throws \Exception
	 */
	public function asArray($withheader = true): array
	{
		if (!$this->cols) {
			throw new \Exception("Must select all or specify cols on constructor");
		}
		$retarr = [];
		if ($withheader) {
			$retarr[] = $this->getHeader();
		}
		foreach ($this->collection as $line) {
			$csvrow = [];
			foreach ($this->cols as $k) {
				$csvrow[] = $line[$k];
			}
			$retarr[] = $csvrow;
		}
		return $retarr;
	}

	/**
	 * This is used to return a streamed response which can then be
	 * proxied off elsewhere
	 *
	 * @param string $filename
	 * @param boolean $withheader
	 * @return \Symfony\Component\HttpFoundation\StreamedResponse
	 * @throws \Exception
	 * @throws \InvalidArgumentException
	 */
	public function asStream(string $filename = "export.csv", bool $withheader = true): StreamedResponse
	{
		if (!$this->cols) {
			throw new \Exception("Must select all or specify cols in __construct");
		}
		$resp = new StreamedResponse();
		$resp->setCallback(function () use ($withheader) {
			$fh = fopen('php://output', 'r+');
			$rows = $this->asArray($withheader);
			foreach ($rows as $csvrow) {
				fputcsv($fh, $csvrow, ",");
			}
			fputs($fh, $this->footer);
			fclose($fh);
		});
		$resp->headers->set('Content-Type', 'application/force-download');
		$resp->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
		return $resp;
	}
}
