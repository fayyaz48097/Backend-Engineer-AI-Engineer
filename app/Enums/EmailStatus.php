<?php

namespace App\Enums;

enum EmailStatus: string
{
    /** Nothing generated yet. */
    case Pending = 'pending';

    /** AI produced a draft; a human has not signed it off. */
    case Draft = 'draft';

    /** Cleared for delivery. */
    case Approved = 'approved';

    /** Handed to the queue. */
    case Queued = 'queued';

    case Sent = 'sent';

    case Rejected = 'rejected';

    case Failed = 'failed';

    /**
     * A draft can be approved; a failed send can be approved again to retry.
     */
    public function canBeApproved(): bool
    {
        return in_array($this, [self::Draft, self::Failed], true);
    }

    /**
     * Guard against sending twice, and against sending something nobody
     * approved. Queued is included so a released job can resume.
     */
    public function canBeSent(): bool
    {
        return in_array($this, [self::Approved, self::Queued], true);
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Sent, self::Rejected], true);
    }
}
