<x-app-layout>
    <x-slot name="header">
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

        <h2 class="font-semibold text-xl text-gray-800 leading-tight text-center">
            {{ __('Lista de Atendimentos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-6 bg-white border-b border-gray-200 flex justify-between items-center">
                    <!-- Área de pesquisa de pacientes -->
                    <div class="w-1/4 block">
                        <form action="{{ route('dashboard') }}" method="GET">
                            <input type="text" id="searchInput" name="search" class="border-gray-300 rounded-md px-4 py-2 w-full bg-white border shadow-sm border-slate-300 placeholder-slate-400 focus:outline-none focus:border-green-800 focus:ring-green-800 block w-full rounded-md sm:text-sm focus:ring-1 focus:ring-opacity-50" placeholder="Nome ou Cartão SUS...">
                            <button class="d-none" type="submit">Search</button>
                        </form>
                    </div>
                    <!-- Ver somente meus atendimentos switch -->
                    <div class="">
                        <form class="flex items-center ml-2" id="dashboardForm" action="{{ route('dashboard') }}" method="GET">
                            <label class="switch flex items-center justify-center">
                                <input type="checkbox" id="switchVerAtendimentos" onchange="meusEncaminhamentos('{{ \Illuminate\Support\Facades\Auth::user()->attention_type }}')">
                                <span class="slider"></span>
                            </label>
                            <span class="ml-2 text-gray-800">Ver somente meus atendimentos</span>
                        </form>
                    </div>
                    <!-- Botão Novo paciente -->
                    <button type="button" class="icon text-white font-bold py-2 px-4 rounded shadow-md" style="background-color: #186f65;" data-toggle="modal" data-target="#modalNovoPaciente">
                        Novo Paciente
                    </button>
                    <!-- Modal -->
                    @include('modaleditar')
                </div>
            </div>
            <!-- Tabela de Atendimentos -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 d-flex flex-column">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="w-1/5 px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Prioridade
                                </th>
                                <th scope="col" class="w-1/5 px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nome
                                </th>
                                <th scope="col" class="w-1/5 px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Identificação SUS
                                </th>
                                <th scope="col" class="w-1/5 px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Data
                                </th>
                                <th scope="col" class="w-1/5 px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ver mais
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($atendimentos as $atendimento)
                                @include('components.table-row')
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Seleciona o campo de entrada de texto
        const searchInput = document.getElementById('searchInput');

        // Adiciona um evento de escuta para o evento de input (quando o usuário digita algo)
        searchInput.addEventListener('input', function() {
            // Obtém o valor digitado pelo usuário e converte para minúsculas para tornar a pesquisa insensível a maiúsculas e minúsculas
            const searchText = searchInput.value.toLowerCase();

            // Seleciona todas as linhas de atendimento
            const atendimentos = document.querySelectorAll('.row');

            // Itera sobre cada atendimento
            atendimentos.forEach(function(atendimento) {
                // Obtém todo o texto dentro do atendimento
                const atendimentoText = atendimento.textContent.toLowerCase();

                // Verifica se o texto de pesquisa está presente em alguma parte do atendimento
                if (atendimentoText.includes(searchText)) {
                    // Se estiver presente, mostra o atendimento
                    atendimento.style.display = '';
                } else {
                    // Se não estiver presente, esconde o atendimento
                    atendimento.style.display = 'none';
                }
            });
        });

        function meusEncaminhamentos(tipoUsuario) {
            const checkbox = document.getElementById('switchVerAtendimentos');
            checkbox.addEventListener('change', function() {
                const form = document.getElementById('dashboardForm');
                const tipoUsuarioAtual = '{{ \Illuminate\Support\Facades\Auth::user()->attention_type }}'; // Obtenha o tipo de usuário de onde for apropriado

                // Verifica se o switch está marcado
                if (checkbox.checked) {
                    // Se estiver marcado, filtra apenas os atendimentos encaminhados para o tipo de usuário atual
                    form.action = '{{ route('dashboard', ['search' => tipoUsuarioAtual]) }}';
                } else {
                    // Se não estiver marcado, volta para a rota padrão sem filtro
                    form.action = "{{ route('dashboard') }}";
                }
                form.submit();
            });
        }

        function converteData(params) {
            var partes = params.split("-");
            return partes[2] + "-" + partes[1] + "-" + partes[0];
        }

        function resgatarDados(atendimento) {
            atendimento = JSON.parse(atendimento);

            document.getElementById('nome').value = atendimento.nome;
            document.getElementById('idade').value = atendimento.idade;
            if (atendimento.sexo && atendimento.sexo === 'masculino') {
                document.getElementById('masculino').checked = true;
            } else {
                document.getElementById('feminino').checked = true;
            }
            document.getElementById('contato').value = atendimento.contato;
            document.getElementById('data-nascimento').value = atendimento.data_nascimento;
            document.getElementById('cartao-sus').value = atendimento.cartao_sus;
            document.getElementById('endereco').value = atendimento.endereco;
            document.getElementById('data-cadastro').value = atendimento.data_cadastro;
            document.getElementById('ubs').value = atendimento.ubs;
            document.getElementById('acs').value = atendimento.acs;
            document.getElementById('diagnostico').value = atendimento.diagnostico;
            document.getElementById('comorbidades').value = atendimento.comorbidades;
            document.getElementById('ultima-internacao').value = atendimento.ultima_internacao;
            document.getElementById('medico-responsavel').value = atendimento.medico_responsavel;

            if (atendimento.prioridade && atendimento.prioridade === 'alta') {
                document.getElementById('alta').checked = true;
            } else if (atendimento.prioridade === 'media') {
                document.getElementById('media').checked = true;
            } else {
                document.getElementById('baixa').checked = true;
            }

            if (atendimento.neurologicas) {
                document.getElementById('neurologicas').checked = true;
            }

            if (atendimento.dor_descricao) {
                document.getElementById('dor').checked = true;
            }
            document.getElementById('dor_descricao').value = atendimento.dor_descricao;

            if (atendimento.incapacidade) {
                document.getElementById('incapacidade').checked = true;
            }
            document.getElementById('avds').value = atendimento.incapacidade_descricao;

            if (atendimento.osteomusculares) {
                document.getElementById('osteomusculares').checked = true;
            }
            document.getElementById('motivos-osteomusculares').value = atendimento.osteomusculares_descricao;

            if (atendimento.uroginecologicas) {
                document.getElementById('uroginecologicas').checked = true;
            }
            document.getElementById('motivos-uroginecologicas').value = atendimento.uroginecologicas_descricao;
            document.getElementById('observacao').value = atendimento.observacao;
        }
    </script>
</x-app-layout>
