window.ImproovMedia = {
  thumb(src, width = 900, quality = 78) {
    if (!src || /^(data:|blob:|https?:\/\/)/i.test(src)) return src;
    return `/improov-site/thumb.php?path=${encodeURIComponent(src)}&w=${width}&q=${quality}`;
  },
};

(function () {
  var STORAGE_KEY = "improov-language";
  var supportedLanguages = ["pt-BR", "en", "es"];
  var translations = {
    "pt-BR": {
      "site.title": "Improov",
      "language.label": "Selecionar idioma",
      "menu.open": "Abrir menu",
      "menu.close": "Fechar menu",
      "nav.home": "Início",
      "nav.about": "Quem somos",
      "nav.projects": "Projetos",
      "nav.contact": "Contato",
      "hero.label": "Vídeo de projetos",
      "projects.label": "Projetos",
      "projects.ayaAlt": "Projeto Aya Kar",
      "projects.cellarAlt": "Projeto Adega",
      "projects.playroomAlt": "Projeto Brinquedoteca",
      "projects.lightAlt": "Projeto Iluminação",
      "projects.spaceAlt": "Projeto Espaço",
      "projects.lobbyAlt": "Projeto Lobby",
      "about.title": "+ Heartmade +",
      "about.p1":
        "“Heartmade” é a nossa filosofia de trabalho; é o nosso selo de customização. Trabalho feito por seres humanos e feito com coração!",
      "about.p2":
        "Essa é a fórmula que usamos para que cada cliente tenha materiais sensoriais originais capazes de conectar seu produto ao seu cliente de forma única.",
      "about.p3":
        "É o contrário de simplificar o desenvolvimento padronizando ou utilizando Inteligência Artificial para criar; nós usamos o coração! É conexão emocional e entusiasmo genuíno pelo trabalho.",
      "about.p4":
        "É a sutileza dos detalhes que sopram vida em tudo o que é criado pela Improov.",
      "contact.title": "Vamos criar juntos?",
      "contact.slogan":
        "Trabalho feito com coração, para conectar marcas e pessoas de forma única.",
      "contact.detailsEyebrow": "FALE COM A GENTE",
      "contact.detailsTitle": "Contato",
      "contact.location": "Blumenau, SC — Brasil",
      "careers.eyebrow": "FAÇA PARTE DO TIME",
      "careers.title": "Trabalhe Conosco",
      "careers.description":
        "Conte-nos como seu olhar pode fazer parte das próximas histórias que criaremos.",
      "form.name": "Nome completo",
      "form.namePlaceholder": "Seu nome",
      "form.email": "E-mail",
      "form.emailPlaceholder": "seu@email.com",
      "form.phone": "Telefone / WhatsApp",
      "form.phonePlaceholder": "(47) 99999-9999",
      "form.city": "Cidade e estado onde reside",
      "form.cityPlaceholder": "Blumenau, SC",
      "form.role": "Área ou cargo de interesse",
      "form.selectArea": "Selecione sua área",
      "form.areaArtDirection": "Direção de arte",
      "form.areaDesign": "Design",
      "form.areaMotion": "Motion e 3D",
      "form.areaArchitecture": "Arquitetura",
      "form.areaProduction": "Produção",
      "form.areaOther": "Outra área",
      "form.start": "Disponibilidade para início",
      "form.selectAvailability": "Selecione",
      "form.availabilityNow": "Imediata",
      "form.availability30": "Em até 30 dias",
      "form.availability60": "Em até 60 dias",
      "form.availabilityOther": "Outra data",
      "form.workModel": "Modelos de trabalho possíveis",
      "form.inPerson": "Presencial",
      "form.inPersonHint": "No escritório",
      "form.hybrid": "Híbrido",
      "form.hybridHint": "Presencial + remoto",
      "form.remote": "Remoto",
      "form.remoteHint": "Trabalho remoto",
      "form.portfolio": "Link do portfólio",
      "form.portfolioPlaceholder": "https://seuportfolio.com",
      "form.resume": "Currículo (PDF até 10MB)",
      "form.resumePrompt": "Clique para anexar seu currículo",
      "form.resumeHint": "PDF até 10MB",
      "form.removeFile": "Remover",
      "form.linkedin": "LinkedIn (opcional)",
      "form.linkedinPlaceholder": "https://linkedin.com/in/seuperfil",
      "form.experience": "Conte um pouco sobre você e sua experiência",
      "form.experiencePlaceholder":
        "Fale sobre sua trajetória, experiências e o que te motiva a fazer parte do nosso time...",
      "form.privacyNote":
        "Seus dados estão protegidos e serão utilizados apenas para processos seletivos.",
      "contact.emailHint": "Responderemos en 24 horas",
      "contact.phoneHint": "Lun a vie, de 9:00 a 18:00",
      "contact.whatsappHint": "WhatsApp",
      "contact.instagramHint": "Sigue nuestro trabajo",
      "contact.locationHint": "Nuestra base creativa",
      "contact.calloutTitle":
        "Únete a quienes transforman ideas en experiencias",
      "contact.calloutText":
        "Buscamos personas apasionadas por crear imágenes que conectan marcas y personas.",
      "form.namePlaceholder": "Tu nombre",
      "form.emailPlaceholder": "tu@email.com",
      "form.phonePlaceholder": "+55 47 99999-9999",
      "form.cityPlaceholder": "Blumenau, SC",
      "form.selectArea": "Selecciona tu área",
      "form.areaArtDirection": "Dirección de arte",
      "form.areaDesign": "Diseño",
      "form.areaMotion": "Motion y 3D",
      "form.areaArchitecture": "Arquitectura",
      "form.areaProduction": "Producción",
      "form.areaOther": "Otra área",
      "form.selectAvailability": "Selecciona",
      "form.availabilityNow": "Inmediata",
      "form.availability30": "En hasta 30 días",
      "form.availability60": "En hasta 60 días",
      "form.availabilityOther": "Otra fecha",
      "form.inPersonHint": "En el estudio",
      "form.hybridHint": "Presencial + remoto",
      "form.remoteHint": "Trabajo remoto",
      "form.portfolioPlaceholder": "https://tuportafolio.com",
      "form.resume": "Currículum (PDF hasta 10MB)",
      "form.resumePrompt": "Haz clic para adjuntar tu currículum",
      "form.resumeHint": "PDF hasta 10MB",
      "form.removeFile": "Eliminar",
      "form.linkedinPlaceholder": "https://linkedin.com/in/tuperfil",
      "form.experience": "Cuéntanos sobre ti y tu experiencia",
      "form.experiencePlaceholder":
        "Cuéntanos sobre tu trayectoria, experiencia y qué te motiva a formar parte de nuestro equipo...",
      "form.privacyNote":
        "Tus datos están protegidos y solo se utilizarán para procesos de selección.",
      "form.privacy":
        'Autorizo o uso destes dados para avaliação da minha candidatura, conforme a <a href="privacidade.html">Política de Privacidade</a>.',
      "form.submit": "Enviar candidatura",
      "form.sending": "Enviando candidatura...",
      "form.successEmailPending":
        "Candidatura registrada. A notificação interna ficou pendente.",
      "form.success":
        "Recebemos sua candidatura. Obrigado por querer criar com a Improov!",
      "form.error":
        "Não foi possível enviar agora. Tente novamente ou envie seu material para contato@improov.com.br.",
      "form.workModelError":
        "Selecione pelo menos um modelo de trabalho possível.",
      "form.setup":
        "O recebimento de candidaturas está sendo preparado. Envie seu material para contato@improov.com.br.",
      "social.label": "Redes sociais",
      "footer.copy": "© 2026 Improov. Todos os direitos reservados.",
      "privacy.title": "Política de Privacidade",
      "privacy.intro": "Privacidade nas candidaturas",
      "privacy.p1":
        "Os dados enviados pelo formulário Trabalhe Conosco são usados exclusivamente para avaliar sua candidatura e entrar em contato sobre oportunidades na Improov.",
      "privacy.p2":
        "Solicitamos apenas informações profissionais necessárias ao processo, como seus dados de contato, cidade, disponibilidade e links de portfólio, currículo e LinkedIn.",
      "privacy.p3":
        "Você pode solicitar esclarecimentos, atualização ou exclusão dos seus dados a qualquer momento pelo e-mail contato@improov.com.br.",
      "privacy.back": "Voltar ao site",
      "project.film": "Filme",
      "project.animations": "Animações",
      "project.related": "Projetos relacionados",
      "project.location": "Localização",
      "project.client": "Cliente",
      "project.architect": "Arquiteto",
      "project.year": "Ano",
      "project.info": "Informações",
      "project.comingSoon": "Em breve",
      "project.descriptionSoon": "Descrição em breve.",
      "project.videoUnavailable": "Filme indisponível.",
      "project.animationsUnavailable": "Sem animações disponíveis.",
      "project.noRelated": "Nenhum projeto relacionado no momento.",
      "project.image": "Imagem",
      "project.browserUnsupported": "Seu navegador não suporta vídeo.",
      "project.close": "Fechar",
      "project.unavailable": "Projeto indisponível",
      "project.loadError": "Não foi possível carregar os dados.",
    },
    en: {
      "site.title": "Improov",
      "language.label": "Select language",
      "menu.open": "Open menu",
      "menu.close": "Close menu",
      "nav.home": "Home",
      "nav.about": "About us",
      "nav.projects": "Projects",
      "nav.contact": "Contact",
      "hero.label": "Project video",
      "projects.label": "Projects",
      "projects.ayaAlt": "Aya Kar project",
      "projects.cellarAlt": "Wine cellar project",
      "projects.playroomAlt": "Playroom project",
      "projects.lightAlt": "Lighting project",
      "projects.spaceAlt": "Space project",
      "projects.lobbyAlt": "Lobby project",
      "about.title": "+ Heartmade +",
      "about.p1":
        "“Heartmade” is our work philosophy; it is our mark of customization. Work made by people and made with heart!",
      "about.p2":
        "This is how we ensure every client has original sensory materials that connect their product to their customers in a unique way.",
      "about.p3":
        "It is the opposite of simplifying development through standardization or by using Artificial Intelligence to create; we use heart. It is emotional connection and genuine enthusiasm for the work.",
      "about.p4":
        "It is the subtlety of details that breathes life into everything created by Improov.",
      "contact.title": "Shall we create together?",
      "contact.slogan":
        "Work made with heart to connect brands and people in a unique way.",
      "contact.detailsEyebrow": "GET IN TOUCH",
      "contact.detailsTitle": "Contact",
      "contact.emailHint": "We will reply within 24 hours",
      "contact.phoneHint": "Mon to Fri, 9am to 6pm",
      "contact.whatsappHint": "WhatsApp",
      "contact.instagramHint": "Follow our work",
      "contact.locationHint": "Our creative base",
      "contact.calloutTitle": "Join those who turn ideas into experiences",
      "contact.calloutText":
        "We look for passionate people who create images that connect brands and people.",
      "contact.location": "Blumenau, SC — Brazil",
      "careers.eyebrow": "JOIN OUR TEAM",
      "careers.title": "Work With Us",
      "careers.description":
        "Tell us how your perspective can become part of the next stories we create.",
      "form.name": "Full name",
      "form.email": "Email",
      "form.phone": "Phone / WhatsApp",
      "form.city": "City and state of residence",
      "form.role": "Area or role of interest",
      "form.start": "Start availability",
      "form.startPlaceholder": "For example: immediately or September 2026",
      "form.workModel": "Possible work arrangements",
      "form.inPerson": "On-site",
      "form.hybrid": "Hybrid",
      "form.remote": "Remote",
      "form.portfolio": "Portfolio link",
      "form.resume": "Résumé link",
      "form.linkedin": "LinkedIn (optional)",
      "form.experience": "Experience or professional summary (optional)",
      "form.message": "Introduction message (optional)",
      "form.namePlaceholder": "Your name",
      "form.emailPlaceholder": "you@email.com",
      "form.phonePlaceholder": "+55 47 99999-9999",
      "form.cityPlaceholder": "Blumenau, SC",
      "form.selectArea": "Select your area",
      "form.areaArtDirection": "Art direction",
      "form.areaDesign": "Design",
      "form.areaMotion": "Motion and 3D",
      "form.areaArchitecture": "Architecture",
      "form.areaProduction": "Production",
      "form.areaOther": "Other area",
      "form.selectAvailability": "Select",
      "form.availabilityNow": "Immediately",
      "form.availability30": "Within 30 days",
      "form.availability60": "Within 60 days",
      "form.availabilityOther": "Another date",
      "form.inPersonHint": "At the studio",
      "form.hybridHint": "On-site + remote",
      "form.remoteHint": "Remote work",
      "form.portfolioPlaceholder": "https://yourportfolio.com",
      "form.resume": "Résumé (PDF up to 10MB)",
      "form.resumePrompt": "Click to attach your résumé",
      "form.resumeHint": "PDF up to 10MB",
      "form.removeFile": "Remove",
      "form.linkedinPlaceholder": "https://linkedin.com/in/yourprofile",
      "form.experience": "Tell us about yourself and your experience",
      "form.experiencePlaceholder":
        "Tell us about your journey, experience, and what motivates you to join our team...",
      "form.privacyNote":
        "Your data is protected and will only be used for recruitment processes.",
      "form.privacy":
        'I authorize the use of this data to assess my application, as described in the <a href="privacidade.html">Privacy Policy</a>.',
      "form.submit": "Submit application",
      "form.sending": "Sending application...",
      "form.successEmailPending":
        "Your application was saved. The internal notification is still pending.",
      "form.success":
        "We received your application. Thank you for wanting to create with Improov!",
      "form.error":
        "We could not send your application now. Please try again or send your materials to contato@improov.com.br.",
      "form.workModelError": "Select at least one possible work arrangement.",
      "form.setup":
        "Application submissions are being prepared. Please send your materials to contato@improov.com.br.",
      "social.label": "Social media",
      "footer.copy": "© 2026 Improov. All rights reserved.",
      "privacy.title": "Privacy Policy",
      "privacy.intro": "Privacy for applications",
      "privacy.p1":
        "The data submitted through the Work With Us form is used solely to assess your application and contact you about opportunities at Improov.",
      "privacy.p2":
        "We request only professional information needed for the process, such as contact details, city, availability, and portfolio, résumé, and LinkedIn links.",
      "privacy.p3":
        "You may request clarification, an update, or deletion of your data at any time by emailing contato@improov.com.br.",
      "privacy.back": "Back to website",
      "project.film": "Film",
      "project.animations": "Animations",
      "project.related": "Related projects",
      "project.location": "Location",
      "project.client": "Client",
      "project.architect": "Architect",
      "project.year": "Year",
      "project.info": "Information",
      "project.comingSoon": "Coming soon",
      "project.descriptionSoon": "Description coming soon.",
      "project.videoUnavailable": "Film unavailable.",
      "project.animationsUnavailable": "No animations available.",
      "project.noRelated": "No related projects at this time.",
      "project.image": "Image",
      "project.browserUnsupported": "Your browser does not support video.",
      "project.close": "Close",
      "project.unavailable": "Project unavailable",
      "project.loadError": "Unable to load the project data.",
    },
    es: {
      "site.title": "Improov",
      "language.label": "Seleccionar idioma",
      "menu.open": "Abrir menú",
      "menu.close": "Cerrar menú",
      "nav.home": "Inicio",
      "nav.about": "Quiénes somos",
      "nav.projects": "Proyectos",
      "nav.contact": "Contacto",
      "hero.label": "Vídeo de proyectos",
      "projects.label": "Proyectos",
      "projects.ayaAlt": "Proyecto Aya Kar",
      "projects.cellarAlt": "Proyecto Bodega",
      "projects.playroomAlt": "Proyecto Ludoteca",
      "projects.lightAlt": "Proyecto Iluminación",
      "projects.spaceAlt": "Proyecto Espacio",
      "projects.lobbyAlt": "Proyecto Lobby",
      "about.title": "+ Heartmade +",
      "about.p1":
        "“Heartmade” es nuestra filosofía de trabajo; es nuestro sello de personalización. ¡Trabajo hecho por seres humanos y hecho con corazón!",
      "about.p2":
        "Esta es la fórmula que utilizamos para que cada cliente tenga materiales sensoriales originales capaces de conectar su producto con su cliente de una manera única.",
      "about.p3":
        "Es lo opuesto a simplificar el desarrollo estandarizando o usando Inteligencia Artificial para crear; nosotros usamos el corazón. Es conexión emocional y entusiasmo genuino por el trabajo.",
      "about.p4":
        "Es la sutileza de los detalles la que da vida a todo lo creado por Improov.",
      "contact.title": "¿Creamos juntos?",
      "contact.slogan":
        "Trabajo hecho con corazón para conectar marcas y personas de una forma única.",
      "contact.detailsEyebrow": "HABLEMOS",
      "contact.detailsTitle": "Contacto",
      "contact.location": "Blumenau, SC — Brasil",
      "contact.emailHint": "Responderemos em até 24h",
      "contact.phoneHint": "Seg a sex, 9h às 18h",
      "contact.whatsappHint": "WhatsApp",
      "contact.instagramHint": "Acompanhe nosso trabalho",
      "contact.locationHint": "Nossa base criativa",
      "contact.calloutTitle":
        "Junte-se a quem transforma ideias em experiências",
      "contact.calloutText":
        "Buscamos pessoas apaixonadas por criar imagens que conectam marcas e pessoas.",
      "careers.eyebrow": "ÚNETE AL EQUIPO",
      "careers.title": "Trabaja con Nosotros",
      "careers.description":
        "Cuéntanos cómo tu mirada puede formar parte de las próximas historias que crearemos.",
      "form.name": "Nombre completo",
      "form.email": "Correo electrónico",
      "form.phone": "Teléfono / WhatsApp",
      "form.city": "Ciudad y estado de residencia",
      "form.role": "Área o cargo de interés",
      "form.start": "Disponibilidad para comenzar",
      "form.startPlaceholder": "Por ejemplo: inmediata o septiembre de 2026",
      "form.workModel": "Modalidades de trabajo posibles",
      "form.inPerson": "Presencial",
      "form.hybrid": "Híbrido",
      "form.remote": "Remoto",
      "form.portfolio": "Enlace del portafolio",
      "form.resume": "Enlace del currículum",
      "form.linkedin": "LinkedIn (opcional)",
      "form.experience": "Experiencia o resumen profesional (opcional)",
      "form.message": "Mensaje de presentación (opcional)",
      "form.privacy":
        'Autorizo el uso de estos datos para evaluar mi candidatura, de acuerdo con la <a href="privacidade.html">Política de Privacidad</a>.',
      "form.submit": "Enviar candidatura",
      "form.sending": "Enviando candidatura...",
      "form.success":
        "Recibimos tu candidatura. ¡Gracias por querer crear con Improov!",
      "form.error":
        "No fue posible enviar la candidatura ahora. Inténtalo de nuevo o envía tu material a contato@improov.com.br.",
      "form.workModelError":
        "Selecciona al menos una modalidad de trabajo posible.",
      "form.setup":
        "El envío de candidaturas está en preparación. Envía tu material a contato@improov.com.br.",
      "social.label": "Redes sociales",
      "footer.copy": "© 2026 Improov. Todos los derechos reservados.",
      "privacy.title": "Política de Privacidad",
      "privacy.intro": "Privacidad en las candidaturas",
      "privacy.p1":
        "Los datos enviados mediante el formulario Trabaja con Nosotros se utilizan exclusivamente para evaluar tu candidatura y contactarte sobre oportunidades en Improov.",
      "privacy.p2":
        "Solicitamos únicamente información profesional necesaria para el proceso, como tus datos de contacto, ciudad, disponibilidad y enlaces de portafolio, currículum y LinkedIn.",
      "privacy.p3":
        "Puedes solicitar aclaraciones, actualización o eliminación de tus datos en cualquier momento escribiendo a contato@improov.com.br.",
      "privacy.back": "Volver al sitio",
      "project.film": "Película",
      "project.animations": "Animaciones",
      "project.related": "Proyectos relacionados",
      "project.location": "Ubicación",
      "project.client": "Cliente",
      "project.architect": "Arquitecto",
      "project.year": "Año",
      "project.info": "Información",
      "project.comingSoon": "Próximamente",
      "project.descriptionSoon": "Descripción próximamente.",
      "project.videoUnavailable": "Película no disponible.",
      "project.animationsUnavailable": "No hay animaciones disponibles.",
      "project.noRelated": "No hay proyectos relacionados por el momento.",
      "project.image": "Imagen",
      "project.browserUnsupported": "Tu navegador no admite vídeo.",
      "project.close": "Cerrar",
      "project.unavailable": "Proyecto no disponible",
      "project.loadError": "No fue posible cargar los datos del proyecto.",
    },
  };

  translations.es["form.successEmailPending"] =
    "La candidatura fue registrada. La notificación interna está pendiente.";
  translations["pt-BR"]["privacy.p4"] =
    "Os dados enviados s\u00e3o registrados em banco de dados e encaminhados por e-mail para o respons\u00e1vel pelo processo seletivo.";
  translations.en["privacy.p4"] =
    "Submitted data is stored in a database and sent by email to the person responsible for the selection process.";
  translations.es["privacy.p4"] =
    "Los datos enviados se registran en una base de datos y se envían por correo electrónico a la persona responsable del proceso de selección.";

  Object.assign(translations["pt-BR"], {
    "contact.emailHint": "Responderemos em até 24h",
    "contact.phoneHint": "Seg a sex, 9h às 18h",
    "contact.whatsappHint": "WhatsApp",
    "contact.instagramHint": "Acompanhe nosso trabalho",
    "contact.locationHint": "Nossa base criativa",
    "contact.calloutTitle": "Junte-se a quem transforma ideias em experiências",
    "contact.calloutText":
      "Buscamos pessoas apaixonadas por criar imagens que conectam marcas e pessoas.",
    "form.namePlaceholder": "Seu nome",
    "form.emailPlaceholder": "seu@email.com",
    "form.phonePlaceholder": "(47) 99999-9999",
    "form.cityPlaceholder": "Blumenau, SC",
    "form.selectArea": "Selecione sua área",
    "form.areaArtDirection": "Direção de arte",
    "form.areaDesign": "Design",
    "form.areaMotion": "Motion e 3D",
    "form.areaArchitecture": "Arquitetura",
    "form.areaProduction": "Produção",
    "form.areaOther": "Outra área",
    "form.selectAvailability": "Selecione",
    "form.availabilityNow": "Imediata",
    "form.availability30": "Em até 30 dias",
    "form.availability60": "Em até 60 dias",
    "form.availabilityOther": "Outra data",
    "form.inPersonHint": "No escritório",
    "form.hybridHint": "Presencial + remoto",
    "form.remoteHint": "Trabalho remoto",
    "form.portfolioPlaceholder": "https://seuportfolio.com",
    "form.resume": "Currículo (PDF até 10MB)",
    "form.resumePrompt": "Clique para anexar seu currículo",
    "form.resumeHint": "PDF até 10MB",
    "form.removeFile": "Remover",
    "form.linkedinPlaceholder": "https://linkedin.com/in/seuperfil",
    "form.experience": "Conte um pouco sobre você e sua experiência",
    "form.experiencePlaceholder":
      "Fale sobre sua trajetória, experiências e o que te motiva a fazer parte do nosso time...",
    "form.privacyNote":
      "Seus dados estão protegidos e serão utilizados apenas para processos seletivos.",
    "form.validation": "Revise os campos destacados antes de enviar.",
    "form.fileRequired": "Anexe seu currículo em PDF.",
    "form.fileTypeError": "Envie apenas arquivos PDF.",
    "form.fileSizeError": "O currículo deve ter no máximo 10MB.",
  });

  Object.assign(translations.es, {
    "contact.emailHint": "Responderemos en 24 horas",
    "contact.phoneHint": "Lun a vie, de 9:00 a 18:00",
    "contact.whatsappHint": "WhatsApp",
    "contact.instagramHint": "Sigue nuestro trabajo",
    "contact.locationHint": "Nuestra base creativa",
    "contact.calloutTitle": "Únete a quienes transforman ideas en experiencias",
    "contact.calloutText":
      "Buscamos personas apasionadas por crear imágenes que conectan marcas y personas.",
    "form.namePlaceholder": "Tu nombre",
    "form.emailPlaceholder": "tu@email.com",
    "form.phonePlaceholder": "+55 47 99999-9999",
    "form.cityPlaceholder": "Blumenau, SC",
    "form.selectArea": "Selecciona tu área",
    "form.areaArtDirection": "Dirección de arte",
    "form.areaDesign": "Diseño",
    "form.areaMotion": "Motion y 3D",
    "form.areaArchitecture": "Arquitectura",
    "form.areaProduction": "Producción",
    "form.areaOther": "Otra área",
    "form.selectAvailability": "Selecciona",
    "form.availabilityNow": "Inmediata",
    "form.availability30": "En hasta 30 días",
    "form.availability60": "En hasta 60 días",
    "form.availabilityOther": "Otra fecha",
    "form.inPersonHint": "En el estudio",
    "form.hybridHint": "Presencial + remoto",
    "form.remoteHint": "Trabajo remoto",
    "form.portfolioPlaceholder": "https://tuportafolio.com",
    "form.resume": "Currículum (PDF hasta 10MB)",
    "form.resumePrompt": "Haz clic para adjuntar tu currículum",
    "form.resumeHint": "PDF hasta 10MB",
    "form.removeFile": "Eliminar",
    "form.linkedinPlaceholder": "https://linkedin.com/in/tuperfil",
    "form.experience": "Cuéntanos sobre ti y tu experiencia",
    "form.experiencePlaceholder":
      "Cuéntanos sobre tu trayectoria, experiencia y qué te motiva a formar parte de nuestro equipo...",
    "form.privacyNote":
      "Tus datos están protegidos y solo se utilizarán para procesos de selección.",
    "form.validation": "Revisa los campos destacados antes de enviar.",
    "form.fileRequired": "Adjunta tu currículum en PDF.",
    "form.fileTypeError": "Envía únicamente archivos PDF.",
    "form.fileSizeError": "El currículum debe pesar como máximo 10MB.",
  });

  Object.assign(translations.en, {
    "form.validation": "Review the highlighted fields before sending.",
    "form.fileRequired": "Attach your résumé as a PDF.",
    "form.fileTypeError": "Please upload a PDF file only.",
    "form.fileSizeError": "The résumé must be no larger than 10MB.",
  });

  var projectTranslations = {
    "aya-kar": {
      en: {
        title: "AYA Karioó",
        description: [
          "At Karioó, comfort, dedicated services, and continuous security come together to create a light and welcoming everyday life.",
          "Karioó is born from earth, time, and memory. In every detail, it leaves the living mark of a belonging that spans time.",
          "What was built was invisible. Here, the feeling is that everything has always belonged to nature.",
        ],
      },
      es: {
        title: "AYA Karioó",
        description: [
          "En Karioó, comodidad, servicios dedicados y seguridad continua se unen para crear una vida cotidiana ligera y acogedora.",
          "Karioó nace de la tierra, del tiempo y del recuerdo. En cada detalle deja la marca viva de una pertenencia que atraviesa el tiempo.",
          "Lo que se construyó fue invisible. Aquí la sensación es que todo siempre ha pertenecido a la naturaleza.",
        ],
      },
    },
    "ars-vie": {
      en: {
        title: "ARS Vieiras",
        description: [
          "At Karioó, comfort, dedicated services, and continuous security come together to create a light and welcoming everyday life.",
          "Karioó is born from earth, time, and memory. In every detail, it leaves the living mark of a belonging that spans time.",
          "What was built was invisible. Here, the feeling is that everything has always belonged to nature.",
        ],
      },
      es: {
        title: "ARS Vieiras",
        description: [
          "En Karioó, comodidad, servicios dedicados y seguridad continua se unen para crear una vida cotidiana ligera y acogedora.",
          "Karioó nace de la tierra, del tiempo y del recuerdo. En cada detalle deja la marca viva de una pertenencia que atraviesa el tiempo.",
          "Lo que se construyó fue invisible. Aquí la sensación es que todo siempre ha pertenecido a la naturaleza.",
        ],
      },
    },
    "academia-energia-urbana": {
      en: {
        title: "Gym — Urban Energy",
        subtitle: "Branding, Architecture, Experience — 2025",
        description: [
          "This project explores the integration of visual identity with architectural space, creating a seamless experience for users. The intervention considers light, color, and materials to convey energy and movement, reinforcing the brand at every touchpoint.",
          "The proposal included layout studies, signage, identity applications in physical and digital media, and scenic lighting solutions to create distinct atmospheres throughout the day.",
        ],
      },
      es: {
        title: "Gimnasio — Energía Urbana",
        subtitle: "Branding, Arquitectura, Experiencia — 2025",
        description: [
          "Este proyecto explora la integración de la identidad visual con el espacio arquitectónico, creando una experiencia fluida para los usuarios. La intervención considera luz, color y materiales para transmitir energía y movimiento, reforzando la marca en cada punto de contacto.",
          "La propuesta incluyó estudios de distribución, señalización, aplicaciones de identidad en medios físicos y digitales, y soluciones de iluminación escenográfica para crear atmósferas distintas a lo largo del día.",
        ],
      },
    },
    "adega-luz-sombra": {
      en: {
        title: "Wine Cellar — Light & Shadow",
        subtitle: "Interiors, Lighting — 2025",
        description: [
          "An interior design project focused on material contrast and indirect light, enhancing the journey and the time spent in the space.",
        ],
      },
      es: {
        title: "Bodega — Luz y Sombra",
        subtitle: "Interiores, Iluminación — 2025",
        description: [
          "Proyecto de ambientación centrado en el contraste de materiales y la luz indirecta, valorizando el recorrido y la permanencia.",
        ],
      },
    },
    "brinquedoteca-cor-movimento": {
      en: {
        title: "Playroom — Color & Movement",
        subtitle: "Interiors, Identity — 2025",
        description: [
          "A playful space focused on chromatic composition and visual orientation for different age groups.",
        ],
      },
      es: {
        title: "Ludoteca — Color y Movimiento",
        subtitle: "Interiores, Identidad — 2025",
        description: [
          "Espacio lúdico centrado en la composición cromática y la orientación visual para diferentes grupos de edad.",
        ],
      },
    },
  };

  function getLanguage() {
    var saved = window.localStorage.getItem(STORAGE_KEY);
    return supportedLanguages.indexOf(saved) !== -1 ? saved : "pt-BR";
  }

  function translate(key, language) {
    var current =
      translations[language || getLanguage()] || translations["pt-BR"];
    return current[key] || translations["pt-BR"][key] || key;
  }

  function applyLanguage(language) {
    var active =
      supportedLanguages.indexOf(language) !== -1 ? language : "pt-BR";
    window.localStorage.setItem(STORAGE_KEY, active);
    document.documentElement.lang = active;
    document.querySelectorAll("[data-i18n]").forEach(function (element) {
      element.textContent = translate(element.dataset.i18n, active);
    });
    document.querySelectorAll("[data-i18n-html]").forEach(function (element) {
      element.innerHTML = translate(element.dataset.i18nHtml, active);
    });
    document
      .querySelectorAll("[data-i18n-placeholder]")
      .forEach(function (element) {
        element.placeholder = translate(
          element.dataset.i18nPlaceholder,
          active,
        );
      });
    document.querySelectorAll("[data-i18n-alt]").forEach(function (element) {
      element.alt = translate(element.dataset.i18nAlt, active);
    });
    document.querySelectorAll("[data-i18n-aria]").forEach(function (element) {
      element.setAttribute(
        "aria-label",
        translate(element.dataset.i18nAria, active),
      );
    });
    document.querySelectorAll("[data-language]").forEach(function (button) {
      button.setAttribute(
        "aria-pressed",
        String(button.dataset.language === active),
      );
    });
    var title = document.querySelector("title");
    if (title && !document.body.classList.contains("project-page"))
      title.textContent = translate("site.title", active);
    var languageInput = document.getElementById("formLanguage");
    if (languageInput) languageInput.value = active;
    document.dispatchEvent(
      new CustomEvent("improov:languagechange", {
        detail: { language: active },
      }),
    );
  }

  (function optimizeGalleryImages() {
    document.querySelectorAll(".galeria-item img").forEach(function (image) {
      var source = image.getAttribute("src");
      if (source && window.ImproovMedia && !source.includes("thumb.php")) {
        image.src = window.ImproovMedia.thumb(source, 900, 78);
      }
    });
    document
      .querySelectorAll(".galeria-item video[poster]")
      .forEach(function (video) {
        var poster = video.getAttribute("poster");
        if (poster && window.ImproovMedia && !poster.includes("thumb.php")) {
          video.setAttribute(
            "poster",
            window.ImproovMedia.thumb(poster, 900, 78),
          );
        }
      });
  })();

  window.ImproovI18n = {
    getLanguage: getLanguage,
    translate: translate,
    projectTranslations: projectTranslations,
    applyLanguage: applyLanguage,
  };

  document.querySelectorAll("[data-language]").forEach(function (button) {
    button.addEventListener("click", function () {
      applyLanguage(button.dataset.language);
    });
  });
  applyLanguage(getLanguage());

  document
    .querySelectorAll(".galeria-item .hover-video")
    .forEach(function (video) {
      var container = video.closest(".galeria-item") || video.parentElement;
      container.addEventListener("mouseenter", function () {
        var play = video.play();
        if (play && play.catch) play.catch(function () {});
      });
      container.addEventListener("mouseleave", function () {
        video.pause();
        try {
          video.currentTime = 0;
        } catch (_error) {}
      });
    });

  (function revealGallery() {
    var items = document.querySelectorAll(".galeria-item");
    if (!items.length) return;
    if (!("IntersectionObserver" in window)) {
      items.forEach(function (item) {
        item.classList.add("in-view");
      });
      return;
    }
    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add("in-view");
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12 },
    );
    items.forEach(function (item, index) {
      item.style.setProperty("--reveal-delay", index * 80 + "ms");
      observer.observe(item);
    });
  })();

  (function mobileMenu() {
    var button = document.querySelector(".hamburger");
    var nav = document.querySelector(".mobile-nav");
    if (!button || !nav) return;
    function setOpen(open) {
      nav.classList.toggle("open", open);
      button.classList.toggle("is-active", open);
      button.setAttribute("aria-expanded", String(open));
      nav.setAttribute("aria-hidden", String(!open));
      document.body.style.overflow = open ? "hidden" : "";
      button.setAttribute(
        "aria-label",
        translate(open ? "menu.close" : "menu.open"),
      );
    }
    button.addEventListener("click", function (event) {
      event.stopPropagation();
      setOpen(!nav.classList.contains("open"));
    });
    nav.querySelectorAll("a").forEach(function (link) {
      link.addEventListener("click", function () {
        setOpen(false);
      });
    });
    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape" && nav.classList.contains("open"))
        setOpen(false);
    });
    document.addEventListener("click", function (event) {
      if (
        nav.classList.contains("open") &&
        !nav.contains(event.target) &&
        !button.contains(event.target)
      )
        setOpen(false);
    });
  })();

  (function careersForm() {
    var form = document.getElementById("careersForm");
    var status = document.getElementById("formStatus");
    var resumeInput = document.getElementById("resumeFile");
    var fileSelected = document.getElementById("fileSelected");
    var fileName = document.getElementById("fileName");
    var fileSize = document.getElementById("fileSize");
    var fileError = document.getElementById("fileError");
    var fileRemove = document.getElementById("fileRemove");
    var characterCount = document.getElementById("characterCount");
    if (!form || !status) return;

    var maxFileSize = 10 * 1024 * 1024;
    var submitting = false;

    if (form.elements.portfolio) form.elements.portfolio.required = false;

    function showStatus(key, type) {
      status.textContent = key ? translate(key) : "";
      status.className = "form-status" + (type ? " is-" + type : "");
    }

    function setFileError(message) {
      if (!fileError) return;
      fileError.textContent = message || "";
      if (resumeInput) resumeInput.setCustomValidity(message || "");
    }

    function formatBytes(bytes) {
      return (bytes / (1024 * 1024)).toFixed(2).replace(".", ",") + " MB";
    }

    function validateFile(file) {
      if (!file) {
        setFileError(translate("form.fileRequired"));
        return false;
      }
      var isPdf = file.type === "application/pdf" || /\.pdf$/i.test(file.name);
      if (!isPdf) {
        setFileError(translate("form.fileTypeError"));
        return false;
      }
      if (file.size > maxFileSize) {
        setFileError(translate("form.fileSizeError"));
        return false;
      }
      setFileError("");
      return true;
    }

    function renderFile(file) {
      if (!fileSelected || !fileName || !fileSize) return;
      fileSelected.hidden = !file;
      fileName.textContent = file ? file.name : "";
      fileSize.textContent = file ? formatBytes(file.size) : "";
    }

    function selectFile(file) {
      if (!validateFile(file)) {
        renderFile(null);
        return;
      }
      var transfer = new DataTransfer();
      transfer.items.add(file);
      resumeInput.files = transfer.files;
      renderFile(file);
    }

    function clearFile() {
      if (resumeInput) resumeInput.value = "";
      setFileError("");
      renderFile(null);
    }

    function updateCharacterCount() {
      var textarea = form.elements.experiencia;
      if (textarea && characterCount) {
        characterCount.textContent = textarea.value.length + "/2000";
      }
    }

    if (resumeInput) {
      resumeInput.addEventListener("change", function () {
        selectFile(resumeInput.files[0]);
      });
    }
    if (fileRemove) fileRemove.addEventListener("click", clearFile);
    var dropzone = form.querySelector(".file-dropzone");
    if (dropzone) {
      ["dragenter", "dragover"].forEach(function (eventName) {
        dropzone.addEventListener(eventName, function (event) {
          event.preventDefault();
          dropzone.classList.add("is-dragging");
        });
      });
      ["dragleave", "drop"].forEach(function (eventName) {
        dropzone.addEventListener(eventName, function (event) {
          event.preventDefault();
          dropzone.classList.remove("is-dragging");
        });
      });
      dropzone.addEventListener("drop", function (event) {
        selectFile(event.dataTransfer.files[0]);
      });
      dropzone.addEventListener("keydown", function (event) {
        if (event.key === "Enter" || event.key === " ") {
          event.preventDefault();
          resumeInput.click();
        }
      });
    }

    if (form.elements.experiencia) {
      form.elements.experiencia.addEventListener("input", updateCharacterCount);
      updateCharacterCount();
    }

    form.addEventListener(
      "invalid",
      function (event) {
        event.target.setAttribute("aria-invalid", "true");
      },
      true,
    );

    form.addEventListener("input", function (event) {
      if (event.target.matches("input, select, textarea")) {
        event.target.removeAttribute("aria-invalid");
      }
    });

    form.addEventListener("submit", function (event) {
      event.preventDefault();
      if (submitting) return;
      showStatus("");
      form.classList.add("was-validated");

      var file = resumeInput && resumeInput.files[0];
      if (!validateFile(file)) {
        showStatus("form.validation", "error");
        resumeInput.focus();
        return;
      }
      if (!form.checkValidity()) {
        showStatus("form.validation", "error");
        var invalid = form.querySelector(":invalid");
        if (invalid) invalid.focus();
        return;
      }

      var endpoint =
        window.ImproovConfig && window.ImproovConfig.applicationEndpoint;
      if (!endpoint) {
        showStatus("form.setup", "error");
        return;
      }

      var submit = form.querySelector("button[type='submit']");
      submitting = true;
      submit.disabled = true;
      submit.setAttribute("aria-busy", "true");
      showStatus("form.sending");

      fetch(endpoint, {
        method: "POST",
        body: new FormData(form),
        headers: { Accept: "application/json" },
      })
        .then(function (response) {
          return response.json().then(function (payload) {
            if (!response.ok || !payload.success) {
              throw new Error(payload.message || "Form submission failed");
            }
            form.reset();
            document.getElementById("formLanguage").value = getLanguage();
            clearFile();
            updateCharacterCount();
            form.querySelectorAll("[aria-invalid]").forEach(function (element) {
              element.removeAttribute("aria-invalid");
            });
            showStatus(
              payload.emailSent === false
                ? "form.successEmailPending"
                : "form.success",
              "success",
            );
          });
        })
        .catch(function () {
          showStatus("form.error", "error");
        })
        .finally(function () {
          submitting = false;
          submit.disabled = false;
          submit.removeAttribute("aria-busy");
        });
    });
  })();
})();
