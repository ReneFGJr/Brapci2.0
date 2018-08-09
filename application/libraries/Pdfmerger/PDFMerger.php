<?php
class PDFMerger {
	private $_files = array();

	/**
	 * Merge PDFs.
	 * @return void
	 */

	public function addPDF($filepath, $pages = 'all') {
		if (file_exists($filepath)) {
			$this -> _files[$filepath] = $pages;
		} else {
			throw new exception("Could not locate PDF on '$filepath'");
		}

		return $this;
	}

	/**
	 * Merges your provided PDFs and outputs to specified location.
	 * @param $outputmode
	 * @param $outputname
	 * @return PDF
	 */
	public function merge($type='',$outputpath = 'newfile.pdf') {
        $outputFileName = $outputpath;
		$files = $this->_files;

        // merge files and save resulting file as PDF version 1.4 for FPDI compatibility
        //$cmd = "/usr/bin/gs -q -dNOPAUSE -dBATCH -dCompatibilityLevel=1.4 -sDEVICE=pdfwrite -sOutputFile=$outputFileName";
        $cmd = "gs -q -dNOPAUSE -dBATCH -dCompatibilityLevel=1.4 -sDEVICE=pdfwrite -sOutputFile=$outputFileName";
        foreach ($files as $key => $value) {
            $cmd .= " $key ";
        }
		echo $cmd;
		exit;
        $result = shell_exec($cmd);
        $this->SetCreator('Your Software Name');
        $this->setPrintHeader(false);
        $numPages = $this->setSourceFile($outputFileName);
        for ($i = 1; $i <= $numPages; $i++) {
            $tplIdx = $this->importPage($i);
            $this->AddPage();
            $this->useTemplate($tplIdx);
        }

        unlink($outputFileName);

        $content = $this->Output(null, 'S');

        return $content;
	}

}
?>