<?php

namespace App\Helpers;

use TCPDF;

class TaskPdf extends TCPDF
{
    public $task;

    public function setTask($task)
    {
        $this->task = $task;
    }

    /* ================= FULL PAGE LETTERHEAD ================= */
    protected function drawLetterhead()
    {
        $this->SetAutoPageBreak(false);

        $this->Image(
            public_path('admin/assets/images/brand-logos/letterhead.png'),
            0,
            0,
            $this->getPageWidth(),
            $this->getPageHeight(),
            '',
            '',
            '',
            false,
            300,
            '',
            false,
            false,
            0,
            false,
            false,
            false
        );

        $this->SetAutoPageBreak(true, 25);
    }

    /* ================= HEADER ================= */
    public function Header()
    {
        // 🔥 Full page BG first
        $this->drawLetterhead();

        // Content start after letterhead header area
        $this->SetY(45);
        $this->SetX(15);

        $this->SetFont('dejavusans', 'B', 12);
        $this->Cell(0, 6, strtoupper($this->task->title), 0, 1);

        $this->SetFont('dejavusans', '', 9);
        $meta = 'Client: ' . ($this->task->client_objective->client->client_name ?? '-')
            . ' | Start: ' . optional($this->task->task_start_date)->format('d M Y')
            . ' | Due: ' . optional($this->task->task_due_date)->format('d M Y');

        $this->Cell(0, 5, $meta, 0, 1);
        $this->SetY($this->tMargin + 10);

    }

    /* ================= FOOTER ================= */
    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('dejavusans', '', 8);

        // Base content width
        $contentWidth = $this->getPageWidth() - $this->lMargin - $this->rMargin;

        // Shift right from center (mm)
        $shift = 25;

        // New width reduced by shift
        $width = $contentWidth - $shift;

        // Start X shifted right
        $this->SetX($this->lMargin + $shift);

        $this->Cell(
            $width,
            5,
            'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(),
            0,
            0,
            'C'
        );
    }
}
