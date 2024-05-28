<div>
    <div wire:ignore id="terminal"></div>
    <script>
        let terminalEnabled = false;
        let command = '';
        let directory = '{{ $currentDirectory }}';

        let commandHistory = [];
        let commandHistoryIndex = -1;

        const interactiveCommands = [
            'php artisan tinker',
            'php artisan serve',
            'php artisan queue:work',
            'php artisan queue:listen',
            'php artisan schedule:work',
        ];

        document.addEventListener('DOMContentLoaded', () => {
            const terminal = new window.Terminal({
                fontFamily: '"Cascadia Code", Menlo, monospace',
                theme: {
                    foreground: '{{ config('laravel-terminal.terminal.colors.foreground') }}',
                    background: '{{ config('laravel-terminal.terminal.colors.background') }}',
                    cursorAccent: '{{ config('laravel-terminal.terminal.colors.cursorAccent') }}',
                    selection: '{{ config('laravel-terminal.terminal.colors.selection') }}',
                    black: '{{ config('laravel-terminal.terminal.colors.black') }}',
                    brightBlack: '{{ config('laravel-terminal.terminal.colors.brightBlack') }}',
                    red: '{{ config('laravel-terminal.terminal.colors.red') }}',
                    brightRed: '{{ config('laravel-terminal.terminal.colors.brightRed') }}',
                    green: '{{ config('laravel-terminal.terminal.colors.green') }}',
                    brightGreen: '{{ config('laravel-terminal.terminal.colors.brightGreen') }}',
                    yellow: '{{ config('laravel-terminal.terminal.colors.yellow') }}',
                    brightYellow: '{{ config('laravel-terminal.terminal.colors.brightYellow') }}',
                    blue: '{{ config('laravel-terminal.terminal.colors.blue') }}',
                    brightBlue: '{{ config('laravel-terminal.terminal.colors.brightBlue') }}',
                    magenta: '{{ config('laravel-terminal.terminal.colors.magenta') }}',
                    brightMagenta: '{{ config('laravel-terminal.terminal.colors.brightMagenta') }}',
                    cyan: '{{ config('laravel-terminal.terminal.colors.cyan') }}',
                    brightCyan: '{{ config('laravel-terminal.terminal.colors.brightCyan') }}',
                    white: '{{ config('laravel-terminal.terminal.colors.white') }}',
                    brightWhite: '{{ config('laravel-terminal.terminal.colors.brightWhite') }}',
                },
                cursorBlink: '{{ config('laravel-terminal.terminal.cursorBlink') }}',
                cols: Math.floor(window.innerWidth / 9),
                rows: Math.floor(window.innerHeight / 19),
            });

            terminal.open(document.getElementById('terminal'));

            @if (config('laravel-terminal.terminal.startup'))
                @foreach(config('laravel-terminal.terminal.startup', []) as $line)
                    terminal.writeln('{{ $line }}');
                @endforeach
            @endif

            @if(config('laravel-terminal.terminal.showInteractiveWarning'))
                terminal.writeln('\x1b[31;1mWarning:\x1b[0m Running interactive commands \x1b[31;1mwill hang your server\x1b[0m indefinitely until restarted.\r\n')
            @endif

            terminal.onData(e => {
                if (!terminalEnabled) return false;
                switch (e) {
                    case '\u0003': // Ctrl+C
                        terminal.write('^C');
                        prompt(terminal);
                        break;
                    case '\r': // Enter
                        if (command.trim().length > 0) {
                            terminalEnabled = false;
                        }
                        runCommand(terminal, command);
                        command = '';
                        break;
                    case '\u007F': // Backspace (DEL)
                        // Do not delete the prompt
                        if (terminal._core.buffer.x > directory.length + 1 + {{ strlen(config('laravel-terminal.terminal.prompt', '$ ')) }}) {
                            terminal.write('\b \b');
                            command = command.substring(0, command.length - 1);
                        }
                        break;
                    case '\u001B\u005B\u0041': // Up arrow
                        // Write the previous command in the history
                        if (commandHistoryIndex < commandHistory.length - 1) {
                            commandHistoryIndex++;
                            command = commandHistory[commandHistoryIndex];
                            terminal.write(`\r${directory} {{ config('laravel-terminal.terminal.prompt', '$') }}` + command + '\x1b[0K');
                        }
                        break;
                    case '\u001B\u005B\u0042': // Down arrow
                        // Write the next command in the history
                        if (commandHistoryIndex >= 0) {
                            commandHistoryIndex--;
                            if (commandHistoryIndex === -1) {
                                command = '';
                                terminal.write(`\r${directory} {{ config('laravel-terminal.terminal.prompt', '$') }}` + '\x1b[0K');
                            } else {
                                command = commandHistory[commandHistoryIndex];
                                terminal.write(`\r${directory} {{ config('laravel-terminal.terminal.prompt', '$') }}` + command + '\x1b[0K');
                            }
                        }
                        break;
                    default:
                        if (e >= String.fromCharCode(0x20) && e <= String.fromCharCode(0x7E) || e >= '\u00a0') {
                            command += e;
                            terminal.write(e);
                        }
                }
            });

            prompt(terminal);

            window.addEventListener('resize', () => {
                terminal.resize(Math.floor(window.innerWidth / 9), Math.floor(window.innerHeight / 19));
            });

            window.addEventListener('laravel-terminal:terminal-output', (event) => {
                if (typeof event.detail.output === 'string') {
                    terminal.writeln(event.detail.output);
                } else if (Array.isArray(event.detail.output)) {
                    event.detail.output.forEach(line => {
                        terminal.writeln(line);
                    })
                }

                prompt(terminal);
            });

            window.addEventListener('laravel-terminal:directory-change', (event) => {
                directory = event.detail.directory;
                prompt(terminal);
            });
        });

        function prompt(terminal) {
            terminal.write(`\r\n${directory} {{ config('laravel-terminal.terminal.prompt', '$') }}`);
            terminalEnabled = true;
            terminal.focus();
        }

        function runCommand(terminal, text) {
            const command = text.trim()

            if (command.length > 0) {
                terminal.writeln('');

                if (interactiveCommands.includes(text)) {
                    terminal.writeln(`\x1b[31;1mError:\x1b[0m Running interactive commands \x1b[31;1mwill hang your server\x1b[0m indefinitely until restarted.`);
                    prompt(terminal);
                    return false;
                }

                commandHistoryIndex = -1; // Reset command history index

                const commandIndex = commandHistory.indexOf(command); // Get index of command in history
                if (commandIndex > -1) { // Check if command already exists in history
                    commandHistory.splice(commandIndex, 1); // Remove command from current position in history
                }
                commandHistory.unshift(command); // Add command to beginning of history

                @this.runCommand(text);
            } else {
                prompt(terminal);
            }
        }
    </script>
</div>
