(function () {
  "use strict";
  const STORAGE_KEY = "improov-language";
  const supported = ["pt-BR", "en", "es"];
  const dictionary = {
    "pt-BR": {
      "nav.home": "Improov — Início",
      "contact.optionImages": "Imagens arquitetônicas",
      "contact.optionFilm": "Filme / animação",
      "contact.optionInteractive": "Experiência interativa",
      "contact.optionOther": "Outro",
      "contact.attachmentHint": "PDF, JPG, PNG ou ZIP — até 20 MB",
      "contact.address":
        "Rua Bahia, 821 — Bairro Do Salto — Blumenau, SC — 89031-001 — Brasil",
      "contact.hours": "Segunda a sexta, das 9h às 18h",
      "form.roleArt": "Direção de arte",
      "form.roleArchitecture": "Arquitetura",
      "form.roleProduction": "Produção",
      "form.roleOther": "Outra área",
      "form.availabilityNow": "Imediata",
      "form.availability30": "Em até 30 dias",
      "form.availability60": "Em até 60 dias",
      "form.availabilityOther": "Outra data",
      "home.heroAlt": "Arquitetura contemporânea integrada à paisagem",
      "about.heroAlt": "Estúdio criativo da Improov em Blumenau",
      "careers.heroAlt": "Equipe trabalhando no estúdio Improov",
      "contact.heroAlt": "Residência contemporânea ao anoitecer",
      "form.careersPrivacy":
        "Autorizo o uso destes dados para avaliação da candidatura conforme a",
      "form.contactPrivacy":
        "Autorizo o uso dos meus dados para contato e envio de propostas conforme a",
      "accessibility.skip": "Pular para o conteúdo",
      "nav.label": "Navegação principal",
      "nav.about": "Quem Somos",
      "nav.projects": "Projetos",
      "nav.careers": "Trabalhe Conosco",
      "nav.contact": "Contato",
      "language.label": "Selecionar idioma",
      "menu.open": "Abrir menu",
      "menu.close": "Fechar menu",
      "whatsapp.label": "Falar com a Improov no WhatsApp",
      "footer.description":
        "Criamos imagens e experiências visuais que conectam pessoas a projetos de arquitetura e ao futuro.",
      "footer.navigation": "Navegação",
      "footer.contact": "Contato",
      "footer.follow": "Siga-nos",
      "footer.rights": "Todos os direitos reservados.",
      "footer.privacy": "Política de Privacidade",
      "home.eyebrow": "Imagens que",
      "home.title": "transformam projetos",
      "home.intro":
        "Criamos imagens arquitetônicas e experiências visuais que conectam pessoas a projetos de arquitetura e ao futuro.",
      "home.action": "Conheça nosso trabalho",
      "home.pillarsEyebrow": "Nossa proposta",
      "home.pillarsTitle": "Imagem com intenção. Experiência com propósito.",
      "home.pillar1": "Foco no essencial",
      "home.pillar1Text":
        "Valorizamos a intenção do projeto e o que realmente importa.",
      "home.pillar2": "Tecnologia e arte",
      "home.pillar2Text": "Ferramentas avançadas a serviço da criatividade.",
      "home.pillar3": "Parceria e entrega",
      "home.pillar3Text": "Caminhamos juntos em todas as etapas.",
      "home.pillar4": "Impacto real",
      "home.pillar4Text": "Imagens que comunicam valor e despertam desejo.",
      "home.projectsTitle": "Conheça alguns dos nossos trabalhos.",
      "home.allProjects": "Ver todos os projetos",
      "projects.eyebrow": "Projetos",
      "projects.title": "Imagens que revelam o essencial de cada projeto.",
      "projects.intro":
        "Desenvolvemos imagens arquitetônicas e experiências visuais que valorizam a intenção do projeto.",
      "projects.label": "Projetos da Improov",
      "projects.project": "Projeto",
      "about.eyebrow": "Quem Somos",
      "about.title": "Quem Somos",
      "about.intro":
        "A Improov nasceu da convicção de que grandes empreendimentos não são vendidos apenas por suas características. Eles conquistam pessoas pelas emoções que despertam.",
      "about.manifestoEyebrow": "Nossa essência",
      "about.manifestoTitle":
        "Grandes empreendimentos conquistam pessoas pelas emoções que despertam.",
      "about.manifestoP1":
        "Somos uma empresa especializada em comunicação para o mercado imobiliário, criando imagens, filmes, animações e experiências visuais capazes de transformar projetos em desejo.",
      "about.manifestoP2":
        "Mais do que representar aquilo que ainda será construído, traduzimos a essência de cada empreendimento. Buscamos revelar sua identidade, sua atmosfera e a história que existe por trás da arquitetura.",
      "about.heartmadeIntro":
        "Nossa metodologia une direção criativa, arte, estratégia e tecnologia para desenvolver materiais que fortalecem marcas, encantam clientes e potencializam resultados comerciais.",
      "about.heartmadeDetails":
        "Cada detalhe é pensado para comunicar com verdade. Cada enquadramento, cada luz, cada movimento e cada narrativa existem para despertar sentimentos.",
      "about.heartmadeLead": "Chamamos essa filosofia de",
      "about.heartmadeTitle": "Heartmade",
      "about.heartmadeBelief":
        "Porque acreditamos que a tecnologia, por si só, impressiona. Mas é o olhar humano que emociona.",
      "about.heartmadeTeam":
        "Ao longo da nossa trajetória, reunimos uma equipe multidisciplinar apaixonada por excelência e comprometida em entregar materiais que elevam o posicionamento de incorporadoras, construtoras e empreendimentos.",
      "about.heartmadeExperience": "Não produzimos apenas imagens.",
      "about.heartmadeClosing":
        "Criamos experiências que fazem pessoas imaginarem, desejarem e acreditarem em um lugar antes mesmo de ele existir.",
      "about.heartmadeFinal":
        "É assim que transformamos arquitetura em comunicação. E comunicação em valor.",
      "about.studioEyebrow": "Nosso Estúdio",
      "about.studioTitle": "O lugar onde as ideias ganham vida.",
      "about.studioText":
        "Um ambiente acolhedor, técnico e criativo, onde colaboração e atenção aos detalhes se encontram todos os dias.",
      "about.locationEyebrow": "Localização",
      "about.directions": "Como chegar",
      "careers.eyebrow": "Trabalhe Conosco",
      "careers.title":
        "Faça parte do time que transforma ideias em experiências visuais.",
      "careers.intro":
        "Somos movidos por curiosidade, colaboração e paixão por imagem.",
      "careers.cultureEyebrow": "Nossa cultura",
      "careers.cultureTitle": "Um ambiente para criar, aprender e evoluir.",
      "careers.value1": "Colaboração real",
      "careers.value1Text": "Acreditamos na força do trabalho em equipe.",
      "careers.value2": "Criatividade com propósito",
      "careers.value2Text": "Transformamos conceitos em imagens com emoção.",
      "careers.value3": "Tecnologia e qualidade",
      "careers.value3Text": "Investimos nas melhores ferramentas e processos.",
      "careers.value4": "Crescimento contínuo",
      "careers.value4Text": "Incentivamos aprendizado e desenvolvimento.",
      "careers.formEyebrow": "Envie sua candidatura",
      "careers.formTitle": "Queremos conhecer você.",
      "careers.studioEyebrow": "Por dentro do estúdio",
      "careers.studio1": "Estúdio em Blumenau, SC",
      "careers.studio2": "Ambiente criativo e colaborativo",
      "careers.studio3": "Projetos desafiadores e autorais",
      "careers.studio4": "Tecnologia e processos de ponta",
      "careers.where": "Onde estamos",
      "contact.eyebrow": "Contato",
      "contact.title": "Vamos conversar sobre seu próximo projeto.",
      "contact.intro":
        "Criamos imagens e experiências visuais que transformam projetos de arquitetura e imobiliário em conexões reais.",
      "contact.whatsapp": "Falar no WhatsApp",
      "contact.formTitle": "Envie sua mensagem",
      "contact.formIntro": "Preencha os campos e retornaremos em breve.",
      "contact.company": "Empresa",
      "contact.interest": "Tipo de projeto / interesse",
      "contact.development": "Nome do empreendimento",
      "contact.message": "Como podemos ajudar?",
      "contact.attachment": "Anexo opcional",
      "contact.submit": "Enviar mensagem",
      "contact.direct": "Fale direto com a gente",
      "contact.phoneLabel": "Telefone / WhatsApp",
      "contact.addressTitle": "Nosso endereço",
      "contact.hoursTitle": "Horário de atendimento",
      "form.name": "Nome completo",
      "form.email": "E-mail",
      "form.phone": "Telefone / WhatsApp",
      "form.city": "Cidade / Estado",
      "form.role": "Área ou cargo",
      "form.availability": "Disponibilidade",
      "form.select": "Selecione",
      "form.workModel": "Modelo de trabalho",
      "form.inPerson": "Presencial",
      "form.hybrid": "Híbrido",
      "form.remote": "Remoto",
      "form.portfolio": "Portfólio",
      "form.resume": "Currículo PDF",
      "form.resumeHint": "PDF até 10 MB",
      "form.upload": "Arraste seu arquivo ou clique para enviar",
      "form.experience": "Conte um pouco sobre você e sua experiência",
      "form.sendApplication": "Enviar candidatura",
      "form.sending": "Enviando...",
      "form.success": "Recebemos suas informações. Obrigado!",
      "form.validation": "Revise os campos destacados.",
      "form.fileSize": "O arquivo excede o limite permitido.",
      "form.error":
        "Não foi possível enviar agora. Tente novamente em instantes.",
      "project.concept": "Conceito",
      "project.client": "Cliente",
      "project.architect": "Arquiteto",
      "project.year": "Ano",
      "project.film": "Filme",
      "cta.eyebrow": "Vamos conversar?",
      "cta.title": "Seu próximo projeto começa com uma boa imagem.",
      "cta.action": "Falar com a gente",
      "privacy.eyebrow": "Privacidade",
      "privacy.title": "Política de Privacidade",
      "privacy.intro":
        "Esta política descreve como tratamos dados enviados pelos formulários da Improov.",
      "privacy.careersTitle": "Candidaturas",
      "privacy.careersText":
        "Dados profissionais e currículo são usados exclusivamente para avaliação e contato sobre oportunidades.",
      "privacy.contactTitle": "Contato comercial",
      "privacy.contactText":
        "Dados de contato, mensagem e anexos são usados para responder solicitações e preparar propostas.",
      "privacy.storageTitle": "Armazenamento e direitos",
      "privacy.storageText":
        "Os dados são armazenados em ambiente controlado e encaminhados ao responsável. O prazo de retenção será formalizado; nenhuma exclusão automática é aplicada neste momento.",
      "privacy.rights":
        "Você pode solicitar acesso, correção ou exclusão pelo e-mail contato@improov.com.br.",
      "privacy.back": "Voltar ao início",
      "notFound.title": "Página não encontrada.",
      "notFound.text": "O endereço pode ter mudado ou não existir.",
      "notFound.back": "Voltar ao início",
    },
    en: {
      "nav.home": "Improov — Home",
      "contact.optionImages": "Architectural images",
      "contact.optionFilm": "Film / animation",
      "contact.optionInteractive": "Interactive experience",
      "contact.optionOther": "Other",
      "contact.attachmentHint": "PDF, JPG, PNG or ZIP — up to 20 MB",
      "contact.address":
        "Rua Bahia, 821 — Bairro Do Salto — Blumenau, SC — 89031-001 — Brazil",
      "contact.hours": "Monday to Friday, 9am to 6pm",
      "form.roleArt": "Art direction",
      "form.roleArchitecture": "Architecture",
      "form.roleProduction": "Production",
      "form.roleOther": "Other area",
      "form.availabilityNow": "Immediately",
      "form.availability30": "Within 30 days",
      "form.availability60": "Within 60 days",
      "form.availabilityOther": "Another date",
      "home.heroAlt": "Contemporary architecture integrated with the landscape",
      "about.heroAlt": "Improov creative studio in Blumenau",
      "careers.heroAlt": "Team working at the Improov studio",
      "contact.heroAlt": "Contemporary residence at dusk",
      "form.careersPrivacy":
        "I authorize the use of this data to assess my application according to the",
      "form.contactPrivacy":
        "I authorize the use of my data for contact and proposals according to the",
      "accessibility.skip": "Skip to content",
      "nav.label": "Main navigation",
      "nav.about": "About Us",
      "nav.projects": "Projects",
      "nav.careers": "Careers",
      "nav.contact": "Contact",
      "language.label": "Select language",
      "menu.open": "Open menu",
      "menu.close": "Close menu",
      "whatsapp.label": "Talk to Improov on WhatsApp",
      "footer.description":
        "We create images and visual experiences that connect people to architecture and the future.",
      "footer.navigation": "Navigation",
      "footer.contact": "Contact",
      "footer.follow": "Follow us",
      "footer.rights": "All rights reserved.",
      "footer.privacy": "Privacy Policy",
      "home.eyebrow": "Images that",
      "home.title": "transform projects",
      "home.intro":
        "We create architectural images and visual experiences that connect people to architecture and the future.",
      "home.action": "Discover our work",
      "home.pillarsEyebrow": "Our proposition",
      "home.pillarsTitle": "Images with intention. Experiences with purpose.",
      "home.pillar1": "Focus on the essential",
      "home.pillar1Text":
        "We value the intention of each project and what truly matters.",
      "home.pillar2": "Technology and art",
      "home.pillar2Text": "Advanced tools at the service of creativity.",
      "home.pillar3": "Partnership and delivery",
      "home.pillar3Text": "We work together throughout every stage.",
      "home.pillar4": "Real impact",
      "home.pillar4Text": "Images that communicate value and inspire desire.",
      "home.projectsTitle": "Discover some of our work.",
      "home.allProjects": "View all projects",
      "projects.eyebrow": "Projects",
      "projects.title": "Images that reveal the essence of every project.",
      "projects.intro":
        "We create architectural images and visual experiences that value the intention behind each project.",
      "projects.label": "Improov projects",
      "projects.project": "Project",
      "about.eyebrow": "About Us",
      "about.title": "About Us",
      "about.intro":
        "Improov was born from the conviction that great developments are not sold by their features alone. They win people over through the emotions they awaken.",
      "about.manifestoEyebrow": "Our essence",
      "about.manifestoTitle":
        "Great developments win people over through the emotions they awaken.",
      "about.manifestoP1":
        "We specialize in communication for the real estate market, creating images, films, animations and visual experiences capable of turning projects into desire.",
      "about.manifestoP2":
        "More than representing what has yet to be built, we translate the essence of each development. We seek to reveal its identity, atmosphere and the story behind its architecture.",
      "about.heartmadeIntro":
        "Our methodology combines creative direction, art, strategy and technology to develop materials that strengthen brands, delight clients and enhance commercial results.",
      "about.heartmadeDetails":
        "Every detail is designed to communicate truthfully. Every frame, light, movement and narrative exists to awaken feelings.",
      "about.heartmadeLead": "We call this philosophy",
      "about.heartmadeTitle": "Heartmade",
      "about.heartmadeBelief":
        "Because we believe technology alone impresses. It is the human eye that moves us.",
      "about.heartmadeTeam":
        "Throughout our journey, we have built a multidisciplinary team passionate about excellence and committed to delivering materials that elevate the positioning of developers, builders and developments.",
      "about.heartmadeExperience": "We do not simply produce images.",
      "about.heartmadeClosing":
        "We create experiences that make people imagine, desire and believe in a place before it even exists.",
      "about.heartmadeFinal":
        "This is how we transform architecture into communication. And communication into value.",
      "about.studioEyebrow": "Our Studio",
      "about.studioTitle": "The place where ideas come to life.",
      "about.studioText":
        "A welcoming, technical and creative environment where collaboration and attention to detail meet every day.",
      "about.locationEyebrow": "Location",
      "about.directions": "Get directions",
      "careers.eyebrow": "Careers",
      "careers.title":
        "Join the team that transforms ideas into visual experiences.",
      "careers.intro":
        "We are driven by curiosity, collaboration and a passion for imagery.",
      "careers.cultureEyebrow": "Our culture",
      "careers.cultureTitle": "A place to create, learn and evolve.",
      "careers.value1": "Real collaboration",
      "careers.value1Text": "We believe in the power of teamwork.",
      "careers.value2": "Creativity with purpose",
      "careers.value2Text": "We transform concepts into emotional images.",
      "careers.value3": "Technology and quality",
      "careers.value3Text": "We invest in excellent tools and processes.",
      "careers.value4": "Continuous growth",
      "careers.value4Text": "We encourage learning and development.",
      "careers.formEyebrow": "Send your application",
      "careers.formTitle": "We want to meet you.",
      "careers.studioEyebrow": "Inside the studio",
      "careers.studio1": "Studio in Blumenau, Brazil",
      "careers.studio2": "Creative and collaborative environment",
      "careers.studio3": "Challenging, authored projects",
      "careers.studio4": "Leading technology and processes",
      "careers.where": "Where we are",
      "contact.eyebrow": "Contact",
      "contact.title": "Let’s talk about your next project.",
      "contact.intro":
        "We create visual experiences that turn architecture and real estate projects into real connections.",
      "contact.whatsapp": "Talk on WhatsApp",
      "contact.formTitle": "Send your message",
      "contact.formIntro":
        "Fill in the fields and we will get back to you soon.",
      "contact.company": "Company",
      "contact.interest": "Project type / interest",
      "contact.development": "Development name",
      "contact.message": "How can we help?",
      "contact.attachment": "Optional attachment",
      "contact.submit": "Send message",
      "contact.direct": "Talk directly to us",
      "contact.phoneLabel": "Phone / WhatsApp",
      "contact.addressTitle": "Our address",
      "contact.hoursTitle": "Business hours",
      "form.name": "Full name",
      "form.email": "Email",
      "form.phone": "Phone / WhatsApp",
      "form.city": "City / State",
      "form.role": "Area or role",
      "form.availability": "Availability",
      "form.select": "Select",
      "form.workModel": "Work model",
      "form.inPerson": "On-site",
      "form.hybrid": "Hybrid",
      "form.remote": "Remote",
      "form.portfolio": "Portfolio",
      "form.resume": "Resume PDF",
      "form.resumeHint": "PDF up to 10 MB",
      "form.upload": "Drop your file or click to upload",
      "form.experience": "Tell us about yourself and your experience",
      "form.sendApplication": "Send application",
      "form.sending": "Sending...",
      "form.success": "We received your information. Thank you!",
      "form.validation": "Please review the highlighted fields.",
      "form.fileSize": "The file exceeds the allowed size.",
      "form.error": "We could not send it now. Please try again shortly.",
      "project.concept": "Concept",
      "project.client": "Client",
      "project.architect": "Architect",
      "project.year": "Year",
      "project.film": "Film",
      "cta.eyebrow": "Shall we talk?",
      "cta.title": "Your next project starts with a great image.",
      "cta.action": "Talk to us",
      "privacy.eyebrow": "Privacy",
      "privacy.title": "Privacy Policy",
      "privacy.intro":
        "This policy describes how we process data submitted through Improov forms.",
      "privacy.careersTitle": "Applications",
      "privacy.careersText":
        "Professional data and resumes are used only to assess applications and discuss opportunities.",
      "privacy.contactTitle": "Commercial contact",
      "privacy.contactText":
        "Contact details, messages and attachments are used to answer inquiries and prepare proposals.",
      "privacy.storageTitle": "Storage and rights",
      "privacy.storageText":
        "Data is stored in a controlled environment. A retention period will be formalized; no automatic deletion is currently applied.",
      "privacy.rights":
        "You may request access, correction or deletion at contato@improov.com.br.",
      "privacy.back": "Back home",
      "notFound.title": "Page not found.",
      "notFound.text": "The address may have changed or may not exist.",
      "notFound.back": "Back home",
    },
    es: {
      "nav.home": "Improov — Inicio",
      "contact.optionImages": "Imágenes arquitectónicas",
      "contact.optionFilm": "Película / animación",
      "contact.optionInteractive": "Experiencia interactiva",
      "contact.optionOther": "Otro",
      "contact.attachmentHint": "PDF, JPG, PNG o ZIP — hasta 20 MB",
      "contact.address":
        "Rua Bahia, 821 — Bairro Do Salto — Blumenau, SC — 89031-001 — Brasil",
      "contact.hours": "Lunes a viernes, de 9h a 18h",
      "form.roleArt": "Dirección de arte",
      "form.roleArchitecture": "Arquitectura",
      "form.roleProduction": "Producción",
      "form.roleOther": "Otra área",
      "form.availabilityNow": "Inmediata",
      "form.availability30": "En hasta 30 días",
      "form.availability60": "En hasta 60 días",
      "form.availabilityOther": "Otra fecha",
      "home.heroAlt": "Arquitectura contemporánea integrada al paisaje",
      "about.heroAlt": "Estudio creativo de Improov en Blumenau",
      "careers.heroAlt": "Equipo trabajando en el estudio Improov",
      "contact.heroAlt": "Residencia contemporánea al anochecer",
      "form.careersPrivacy":
        "Autorizo el uso de estos datos para evaluar mi candidatura según la",
      "form.contactPrivacy":
        "Autorizo el uso de mis datos para contacto y propuestas según la",
      "accessibility.skip": "Saltar al contenido",
      "nav.label": "Navegación principal",
      "nav.about": "Quiénes Somos",
      "nav.projects": "Proyectos",
      "nav.careers": "Trabaja con Nosotros",
      "nav.contact": "Contacto",
      "language.label": "Seleccionar idioma",
      "menu.open": "Abrir menú",
      "menu.close": "Cerrar menú",
      "whatsapp.label": "Hablar con Improov por WhatsApp",
      "footer.description":
        "Creamos imágenes y experiencias visuales que conectan personas con la arquitectura y el futuro.",
      "footer.navigation": "Navegación",
      "footer.contact": "Contacto",
      "footer.follow": "Síguenos",
      "footer.rights": "Todos los derechos reservados.",
      "footer.privacy": "Política de Privacidad",
      "home.eyebrow": "Imágenes que",
      "home.title": "transforman proyectos",
      "home.intro":
        "Creamos imágenes arquitectónicas y experiencias visuales que conectan personas con la arquitectura y el futuro.",
      "home.action": "Conoce nuestro trabajo",
      "home.pillarsEyebrow": "Nuestra propuesta",
      "home.pillarsTitle": "Imagen con intención. Experiencia con propósito.",
      "home.pillar1": "Foco en lo esencial",
      "home.pillar1Text":
        "Valoramos la intención del proyecto y lo que realmente importa.",
      "home.pillar2": "Tecnología y arte",
      "home.pillar2Text":
        "Herramientas avanzadas al servicio de la creatividad.",
      "home.pillar3": "Alianza y entrega",
      "home.pillar3Text": "Caminamos juntos en cada etapa.",
      "home.pillar4": "Impacto real",
      "home.pillar4Text": "Imágenes que comunican valor y despiertan deseo.",
      "home.projectsTitle": "Conoce algunos de nuestros trabajos.",
      "home.allProjects": "Ver todos los proyectos",
      "projects.eyebrow": "Proyectos",
      "projects.title": "Imágenes que revelan lo esencial de cada proyecto.",
      "projects.intro":
        "Creamos imágenes arquitectónicas y experiencias que valoran la intención de cada proyecto.",
      "projects.label": "Proyectos de Improov",
      "projects.project": "Proyecto",
      "about.eyebrow": "Quiénes Somos",
      "about.title": "Quiénes Somos",
      "about.intro":
        "Improov nació de la convicción de que los grandes proyectos inmobiliarios no se venden solo por sus características. Conquistan a las personas por las emociones que despiertan.",
      "about.manifestoEyebrow": "Nuestra esencia",
      "about.manifestoTitle":
        "Los grandes proyectos conquistan a las personas por las emociones que despiertan.",
      "about.manifestoP1":
        "Somos una empresa especializada en comunicación para el mercado inmobiliario, creando imágenes, películas, animaciones y experiencias visuales capaces de transformar proyectos en deseo.",
      "about.manifestoP2":
        "Más que representar aquello que aún será construido, traducimos la esencia de cada proyecto. Buscamos revelar su identidad, su atmósfera y la historia que existe detrás de la arquitectura.",
      "about.heartmadeIntro":
        "Nuestra metodología une dirección creativa, arte, estrategia y tecnología para desarrollar materiales que fortalecen marcas, encantan a clientes y potencian resultados comerciales.",
      "about.heartmadeDetails":
        "Cada detalle está pensado para comunicar con verdad. Cada encuadre, cada luz, cada movimiento y cada narrativa existen para despertar sentimientos.",
      "about.heartmadeLead": "Llamamos a esta filosofía",
      "about.heartmadeTitle": "Heartmade",
      "about.heartmadeBelief":
        "Porque creemos que la tecnología, por sí sola, impresiona. Pero es la mirada humana la que emociona.",
      "about.heartmadeTeam":
        "A lo largo de nuestra trayectoria, reunimos un equipo multidisciplinario apasionado por la excelencia y comprometido con entregar materiales que elevan el posicionamiento de desarrolladores, constructoras y proyectos.",
      "about.heartmadeExperience": "No producimos solo imágenes.",
      "about.heartmadeClosing":
        "Creamos experiencias que hacen que las personas imaginen, deseen y crean en un lugar incluso antes de que exista.",
      "about.heartmadeFinal":
        "Así transformamos la arquitectura en comunicación. Y la comunicación en valor.",
      "about.studioEyebrow": "Nuestro Estudio",
      "about.studioTitle": "El lugar donde las ideas cobran vida.",
      "about.studioText":
        "Un ambiente acogedor, técnico y creativo donde colaboración y atención al detalle se encuentran cada día.",
      "about.locationEyebrow": "Ubicación",
      "about.directions": "Cómo llegar",
      "careers.eyebrow": "Trabaja con Nosotros",
      "careers.title":
        "Forma parte del equipo que transforma ideas en experiencias visuales.",
      "careers.intro":
        "Nos mueven la curiosidad, la colaboración y la pasión por la imagen.",
      "careers.cultureEyebrow": "Nuestra cultura",
      "careers.cultureTitle": "Un ambiente para crear, aprender y evolucionar.",
      "careers.value1": "Colaboración real",
      "careers.value1Text": "Creemos en la fuerza del trabajo en equipo.",
      "careers.value2": "Creatividad con propósito",
      "careers.value2Text": "Transformamos conceptos en imágenes con emoción.",
      "careers.value3": "Tecnología y calidad",
      "careers.value3Text": "Invertimos en excelentes herramientas y procesos.",
      "careers.value4": "Crecimiento continuo",
      "careers.value4Text": "Impulsamos el aprendizaje y el desarrollo.",
      "careers.formEyebrow": "Envía tu candidatura",
      "careers.formTitle": "Queremos conocerte.",
      "careers.studioEyebrow": "Dentro del estudio",
      "careers.studio1": "Estudio en Blumenau, Brasil",
      "careers.studio2": "Ambiente creativo y colaborativo",
      "careers.studio3": "Proyectos desafiantes y autorales",
      "careers.studio4": "Tecnología y procesos de vanguardia",
      "careers.where": "Dónde estamos",
      "contact.eyebrow": "Contacto",
      "contact.title": "Hablemos de tu próximo proyecto.",
      "contact.intro":
        "Creamos experiencias visuales que convierten proyectos de arquitectura e inmobiliarios en conexiones reales.",
      "contact.whatsapp": "Hablar por WhatsApp",
      "contact.formTitle": "Envía tu mensaje",
      "contact.formIntro": "Completa los campos y responderemos pronto.",
      "contact.company": "Empresa",
      "contact.interest": "Tipo de proyecto / interés",
      "contact.development": "Nombre del emprendimiento",
      "contact.message": "¿Cómo podemos ayudar?",
      "contact.attachment": "Adjunto opcional",
      "contact.submit": "Enviar mensaje",
      "contact.direct": "Habla directamente con nosotros",
      "contact.phoneLabel": "Teléfono / WhatsApp",
      "contact.addressTitle": "Nuestra dirección",
      "contact.hoursTitle": "Horario de atención",
      "form.name": "Nombre completo",
      "form.email": "E-mail",
      "form.phone": "Teléfono / WhatsApp",
      "form.city": "Ciudad / Estado",
      "form.role": "Área o cargo",
      "form.availability": "Disponibilidad",
      "form.select": "Selecciona",
      "form.workModel": "Modalidad de trabajo",
      "form.inPerson": "Presencial",
      "form.hybrid": "Híbrido",
      "form.remote": "Remoto",
      "form.portfolio": "Portafolio",
      "form.resume": "Currículum PDF",
      "form.resumeHint": "PDF hasta 10 MB",
      "form.upload": "Arrastra tu archivo o haz clic para enviar",
      "form.experience": "Cuéntanos sobre ti y tu experiencia",
      "form.sendApplication": "Enviar candidatura",
      "form.sending": "Enviando...",
      "form.success": "Recibimos tu información. ¡Gracias!",
      "form.validation": "Revisa los campos destacados.",
      "form.fileSize": "El archivo supera el límite permitido.",
      "form.error":
        "No pudimos enviarlo ahora. Inténtalo de nuevo en unos instantes.",
      "project.concept": "Concepto",
      "project.client": "Cliente",
      "project.architect": "Arquitecto",
      "project.year": "Año",
      "project.film": "Película",
      "cta.eyebrow": "¿Hablamos?",
      "cta.title": "Tu próximo proyecto comienza con una buena imagen.",
      "cta.action": "Hablar con nosotros",
      "privacy.eyebrow": "Privacidad",
      "privacy.title": "Política de Privacidad",
      "privacy.intro":
        "Esta política describe cómo tratamos los datos enviados por los formularios de Improov.",
      "privacy.careersTitle": "Candidaturas",
      "privacy.careersText":
        "Los datos profesionales y currículums se usan solamente para evaluar candidaturas y oportunidades.",
      "privacy.contactTitle": "Contacto comercial",
      "privacy.contactText":
        "Los datos, mensajes y adjuntos se usan para responder solicitudes y preparar propuestas.",
      "privacy.storageTitle": "Almacenamiento y derechos",
      "privacy.storageText":
        "Los datos se guardan en un entorno controlado. El plazo de retención será formalizado; actualmente no hay eliminación automática.",
      "privacy.rights":
        "Puedes solicitar acceso, corrección o eliminación en contato@improov.com.br.",
      "privacy.back": "Volver al inicio",
      "notFound.title": "Página no encontrada.",
      "notFound.text": "La dirección puede haber cambiado o no existir.",
      "notFound.back": "Volver al inicio",
    },
  };

  function getLanguage() {
    const saved = localStorage.getItem(STORAGE_KEY);
    return supported.includes(saved) ? saved : "pt-BR";
  }
  function translate(key, language) {
    const lang = language || getLanguage();
    return dictionary[lang]?.[key] || dictionary["pt-BR"][key] || key;
  }
  function applyLanguage(language) {
    const lang = supported.includes(language) ? language : "pt-BR";
    localStorage.setItem(STORAGE_KEY, lang);
    document.documentElement.lang = lang;
    document.querySelectorAll("[data-i18n]").forEach((el) => {
      el.textContent = translate(el.dataset.i18n, lang);
    });
    document.querySelectorAll("[data-i18n-aria]").forEach((el) => {
      el.setAttribute("aria-label", translate(el.dataset.i18nAria, lang));
    });
    document.querySelectorAll("[data-i18n-placeholder]").forEach((el) => {
      el.placeholder = translate(el.dataset.i18nPlaceholder, lang);
    });
    document.querySelectorAll("[data-i18n-alt]").forEach((el) => {
      el.alt = translate(el.dataset.i18nAlt, lang);
    });
    document.querySelectorAll("[data-i18n-html]").forEach((el) => {
      const key = el.dataset.i18nHtml;
      if (key === "form.careersPrivacy" || key === "form.contactPrivacy")
        return;
      el.innerHTML = translate(key, lang);
    });
    document
      .querySelectorAll("[data-language]")
      .forEach((button) =>
        button.setAttribute(
          "aria-pressed",
          String(button.dataset.language === lang),
        ),
      );
    document.querySelectorAll("[data-language-input]").forEach((input) => {
      input.value = lang;
    });
    document.dispatchEvent(
      new CustomEvent("improov:languagechange", { detail: { language: lang } }),
    );
  }
  window.ImproovI18n = { getLanguage, translate, applyLanguage };
  document.addEventListener("DOMContentLoaded", () => {
    document
      .querySelectorAll("[data-language]")
      .forEach((button) =>
        button.addEventListener("click", () =>
          applyLanguage(button.dataset.language),
        ),
      );
    applyLanguage(getLanguage());
  });
})();
