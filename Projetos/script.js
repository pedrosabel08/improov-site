const DATA_URL = "/improov-site/assets/projetos/projetos.json";

function normalizeProjects(data) {
  if (Array.isArray(data)) return data;
  if (Array.isArray(data?.projects)) return data.projects;
  return [];
}

function createMediaMarkup(projeto) {
  const thumb = projeto.thumbnail || projeto.heroImage;
  if (thumb) {
    return `<img src="${thumb}" alt="${projeto.title || projeto.nome || "Projeto"}">`;
  }

  if (projeto.media?.video || projeto.video) {
    const videoSrc = projeto.media?.video || projeto.video;
    return `
            <video autoplay muted playsinline loop>
                <source src="${videoSrc}" type="video/mp4">
            </video>
        `;
  }

  return '<div class="project-placeholder">Sem mídia</div>';
}

function displayProjects(projetos) {
  const projectsContainer = document.querySelector(".projects");
  projectsContainer.innerHTML = "";

  projetos.forEach((projeto) => {
    const slug = projeto.slug || String(projeto.id || "");
    const title = projeto.title || projeto.nome || "Projeto sem nome";

    const projectDiv = document.createElement("div");
    projectDiv.classList.add("project");
    projectDiv.onclick = () => {
      window.location.href = `/improov-site/Projetos/teste/?slug=${slug}`;
    };

    projectDiv.innerHTML = `
            ${createMediaMarkup(projeto)}
            <h3>${title}</h3>
        `;

    projectsContainer.appendChild(projectDiv);
  });
}

fetch(DATA_URL)
  .then((response) => response.json())
  .then((data) => {
    const projetos = normalizeProjects(data);
    displayProjects(projetos);
  })
  .catch((error) => {
    console.error("Erro ao carregar projetos:", error);
  });
