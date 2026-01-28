let projetos = [];

// Função para carregar dados do JSON
fetch('projetos.json')
    .then(response => response.json())
    .then(data => {
        projetos = data;
        displayProjects(); // Chama a função para exibir os projetos
    });

function displayProjects() {
    const projectsContainer = document.querySelector('.projects');
    projetos.forEach(projeto => {
        const projectDiv = document.createElement('div');
        projectDiv.classList.add('project');

        // Adiciona evento de clique que redireciona para a página de detalhes
        projectDiv.onclick = () => {
            window.location.href = `detalhes.html?id=${projeto.id}`;
        };

        projectDiv.innerHTML = `
            <video autoplay muted playsinline loop>
                <source src="${projeto.video}" type="video/mp4">
            </video>
            <h3>${projeto.nome}</h3>
        `;
        projectsContainer.appendChild(projectDiv); // Adiciona o projeto à seção
    });
}