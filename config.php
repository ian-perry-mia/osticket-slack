<?php

require_once INCLUDE_DIR . 'class.plugin.php';

class SlackPluginConfig extends PluginConfig
{

    // Provide compatibility function for versions of osTicket prior to
    // translation support (v1.9.4)
    function translate()
    {
        if (!method_exists('Plugin', 'translate')) {
            return array(
                function ($x) {
                    return $x;
                },
                function ($x, $y, $n) {
                    return $n != 1 ? $y : $x;
                }
            );
        }
        return Plugin::translate('slack');
    }

    function pre_save(&$config, &$errors)
    {
        if ($config['slack-regex-subject-ignore'] && false === @preg_match("/{$config['slack-regex-subject-ignore']}/i", null)) {
            $errors['err'] = 'Your regex was invalid, try something like "spam", it will become: "/spam/i" when we use it.';
            return FALSE;
        }

        return TRUE;
    }

    function getOptions()
    {
        list($__, $_N) = self::translate();

        return array(
            new SectionBreakField([
                'id' => 'slack',
                'label' => 'Slack notifier',
                'hint' => 'Readme first: https://github.com/ian-perry-mia/osticket-slack'
            ]),

            'slack-webhook-url' => new TextboxField([
                'id' => 'slack-webhook-url',
                'label' => 'Webhook URL',
                'required' => true,
                'configuration' => [
                    'size' => 100,
                    'length' => 200
                ],
            ]),

            'slack-regex-subject-ignore' => new TextboxField([
                'id' => 'slack-regex-subject-ignore',
                'label' => 'Ignore when subject equals regex',
                'hint' => 'Auto delimited, always case-insensitive',
                'configuration' => [
                    'size' => 30,
                    'length' => 200
                ],
            ]),

            new SectionBreakField([
                'label' => $__('Conditions to notify on')
            ]),

            'slack-update-ticket-opened' =>new BooleanField([
                'id' => 'slack-update-ticket-opened',
                'label' => 'Ticket Opened'
            ]),
            'slack-update-ticket-opened-color' =>new TextboxField([
                'id' => 'slack-update-ticket-opened-color',
                'label' => 'HEX code for Ticket Opened color',
                'default' => '#36a64f',
                'hint' => 'Optional: Specify a HEX color code for the Slack message when a ticket is opened, e.g. #36a64f',
                'configuration' => [
                    'size' => 30,
                    'length' => 7
                ],
            ]),

            'slack-update-ticket-internal-note' => new BooleanField([
                'id' => 'slack-update-ticket-internal-note',
                'label' => 'Ticket Internal Note'
            ]),
            'slack-update-ticket-internal-note-color' => new TextboxField([
                'id' => 'slack-update-ticket-internal-note-color',
                'label' => 'HEX code for Ticket Internal Note color',
                'default' => '#aa00ff',
                'hint' => 'Optional: Specify a HEX color code for the Slack message when a ticket is replied to, e.g. #aa00ff',
                'configuration' => [
                    'size' => 30,
                    'length' => 7
                ],
            ]),

            'slack-update-ticket-agent-reply' => new BooleanField([
                'id' => 'slack-update-ticket-agent-reply',
                'label' => 'Ticket Agent Reply'
            ]),
            'slack-update-ticket-agent-reply-color' => new TextboxField([
                'id' => 'slack-update-ticket-agent-reply-color',
                'label' => 'HEX code for Ticket Agent Reply color',
                'default' => '#aa00ff',
                'hint' => 'Optional: Specify a HEX color code for the Slack message when a ticket is replied to, e.g. #aa00ff',
                'configuration' => [
                    'size' => 30,
                    'length' => 7
                ],
            ]),

            'slack-update-ticket-user-reply' => new BooleanField([
                'id' => 'slack-update-ticket-user-reply',
                'label' => 'Ticket User Reply'
            ]),
            'slack-update-ticket-user-reply-color' => new TextboxField([
                'id' => 'slack-update-ticket-user-reply-color',
                'label' => 'HEX code for Ticket User Reply color',
                'default' => '#aa00ff',
                'hint' => 'Optional: Specify a HEX color code for the Slack message when a ticket is replied to, e.g. #aa00ff',
                'configuration' => [
                    'size' => 30,
                    'length' => 7
                ],
            ]),

            'slack-update-ticket-stale-color' => new TextboxField([
                'id' => 'slack-update-ticket-stale-color',
                'label' => 'HEX code for Overdue Ticket color',
                'default' => '#b21111',
                'hint' => 'Optional: Specify a HEX color code to override ticket color when overdue, e.g. #b21111',
                'configuration' => [
                    'size' => 30,
                    'length' => 7
                ],
            ]),

            'message-template' => new TextareaField([
                'label' => $__('Message Template'),
                'hint' => $__('The main text part of the Slack message, uses Ticket Variables, for what the user typed, use variable: %{slack_safe_message}'),
                // "<%{url}/scp/tickets.php?id=%{ticket.id}|%{ticket.subject}>\n" // Already included as Title
                'default' => "%{ticket.name.full} (%{ticket.email}) in *%{ticket.dept}* _%{ticket.topic}_\n\n```%{slack_safe_message}```",
                'configuration' => [
                    'html' => FALSE,
                ]
            ])
        );
    }

}
