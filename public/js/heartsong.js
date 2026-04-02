// ═══════════════════════════════════════════════════════════════
// HEART SONG QUIZ — "What Kind of Paladin Are YOU?"
// ═══════════════════════════════════════════════════════════════
// Inspired by The Elder Scrolls II: Daggerfall character generation
// and Bruce Lee's philosophy of the One Finger.
//
// "The successful warrior is the average man, with laser-like focus."
//   — Bruce Lee
//
// Everyone has that one thing that makes their heart sing.
// To achieve Heart Song, you must align yourself with your heart.
// Only you know that one thing.
//
// For Protestants: "Many are called, but few are chosen" (Matthew 22:14)
// My advice as a preacher: First you must choose yourself.
// Then you must choose to make yourself worthy of being chosen.
//
// For Non-Protestants: I respect all beliefs and traditions. I am a
// protestant. I will not specify it into a denomination, because I
// align with Matthew 22:36-40. That is my Heart Song. I share mine
// to help you find yours.
//
// — David Leo Sylvester, Rose Ministries Ordained
// ═══════════════════════════════════════════════════════════════

const HEART_SONG_QUIZ = {

  // ─── INTRO TEXT ───
  intro: {
    title: "The Heart Song Quiz",
    subtitle: "What Kind of Paladin Are YOU?",
    philosophy: `Bruce Lee once said: "I fear not the man who has practiced 10,000 kicks once, but I fear the man who has practiced one kick 10,000 times." That is the philosophy of the One Finger — the idea that everyone has one thing that makes their heart sing. One calling that, when you pursue it, everything else falls into alignment.`,
    preacher_note: `As an ordained minister through Rose Ministries, I offer this: For those of the Christian tradition, consider Matthew 22:14 — "Many are called, but few are chosen." My counsel is this: first, you must choose yourself. Then you must choose to make yourself worthy of being chosen. For those of other traditions or no tradition at all — I respect all beliefs deeply. I align with Matthew 22:36-40: love God with everything you have, and love your neighbor as yourself. That is my Heart Song. I share mine to help you find yours.`,
    instructions: `Answer these 30 questions honestly. There are no wrong answers. Each question reveals something about where your heart naturally pulls. At the end, the quiz will recommend a Color Order — your meta-category as a Paladin. The recommendation is a mirror, not a cage. You can always choose differently. But first, listen to what your heart is already telling you.`,
    disclaimer: `This quiz is a guide, not a diagnosis. It uses your answers to identify patterns in your values, instincts, and aspirations. The recommended Order is where the engine believes your Heart Song resonates most strongly — and it will explain why.`,
  },

  // ─── 30 QUESTIONS ───
  // Each answer distributes points across the 10 orders.
  // Scoring: each answer has a weight map { orderId: points }
  // Most answers give 3 to the primary order, 1-2 to related orders.

  questions: [
    // === BLOCK 1: Core Values (Q1-6) ===
    {
      id: 1,
      text: "When you close your eyes and imagine the BEST version of yourself, what are you doing?",
      category: "Core Identity",
      answers: [
        { text: "Praying, meditating, or in deep communion with something greater than myself", weights: { white: 3, pink: 1 } },
        { text: "Working behind the scenes to fix a problem nobody else even sees", weights: { black: 3, blue: 1 } },
        { text: "Standing on land I care for, watching something I planted grow", weights: { brown: 3, green: 1 } },
        { text: "Reading, researching, lost in a breakthrough insight", weights: { purple: 3, yellow: 1 } },
        { text: "Leading a team through a crisis with calm authority", weights: { blue: 3, orange: 1 } },
      ],
    },
    {
      id: 2,
      text: "What makes you angriest in the world?",
      category: "Core Values",
      answers: [
        { text: "When people lose faith or hope and no one is there to help them find it again", weights: { white: 3, green: 1 } },
        { text: "When powerful people get away with hurting others because nobody is watching", weights: { black: 3, blue: 1 } },
        { text: "When land, resources, or heritage are wasted or destroyed carelessly", weights: { brown: 3, green: 1 } },
        { text: "When ignorance goes unchallenged and bad ideas spread because nobody speaks up", weights: { purple: 3, yellow: 1 } },
        { text: "When someone who could have been saved wasn't, because help came too late", weights: { orange: 3, red: 1 } },
      ],
    },
    {
      id: 3,
      text: "A child you care about asks: \"What should I be when I grow up?\" What do you tell them?",
      category: "Core Values",
      answers: [
        { text: "\"Listen to what your soul is telling you. It already knows.\"", weights: { white: 3, pink: 1 } },
        { text: "\"Learn to see what others miss. The world needs people who pay attention.\"", weights: { black: 3, purple: 1 } },
        { text: "\"Find something to build that will still be standing when you're gone.\"", weights: { brown: 2, yellow: 2, green: 1 } },
        { text: "\"Never stop learning. The more you understand, the more you can help.\"", weights: { purple: 3, yellow: 1 } },
        { text: "\"Be brave. The world has enough careful people. Be the one who acts.\"", weights: { orange: 3, red: 1 } },
      ],
    },
    {
      id: 4,
      text: "You're exhausted after a brutal week. What restores you?",
      category: "Self-Knowledge",
      answers: [
        { text: "Silence. Prayer. Being alone with my thoughts and something sacred.", weights: { white: 3 } },
        { text: "Analyzing what went wrong and building a plan so it never happens again.", weights: { black: 2, purple: 2 } },
        { text: "Being outdoors. Dirt under my nails. Animals nearby. Sky overhead.", weights: { brown: 3 } },
        { text: "Creating something — writing, coding, building, composing.", weights: { yellow: 2, pink: 2 } },
        { text: "Physical exertion. Gym. Sparring. Running until my mind clears.", weights: { red: 3, orange: 1 } },
      ],
    },
    {
      id: 5,
      text: "Which sentence resonates most deeply with who you ARE (not who you wish you were)?",
      category: "Core Identity",
      answers: [
        { text: "\"I believe there is a purpose to all of this, and I want to serve it.\"", weights: { white: 3, pink: 1 } },
        { text: "\"I see the game behind the game, and I play to protect, not to win.\"", weights: { black: 3, blue: 1 } },
        { text: "\"I am rooted. My strength comes from where I stand and what I steward.\"", weights: { brown: 3 } },
        { text: "\"The truth matters more than comfort. I'll follow it wherever it leads.\"", weights: { purple: 3, black: 1 } },
        { text: "\"When I love someone, I love them with everything. That's my superpower.\"", weights: { pink: 3, green: 1 } },
      ],
    },
    {
      id: 6,
      text: "If you could master ONE thing in this lifetime, what would it be?",
      category: "The One Finger",
      answers: [
        { text: "Understanding the divine / the transcendent / the meaning behind existence", weights: { white: 3, purple: 1 } },
        { text: "Strategy — reading situations and people so clearly that I'm always three moves ahead", weights: { black: 3, blue: 1 } },
        { text: "Stewardship — caring for land, animals, people, and resources so they thrive for generations", weights: { brown: 3, green: 1 } },
        { text: "Knowledge — understanding how the universe actually works at a fundamental level", weights: { purple: 3, yellow: 1 } },
        { text: "My body — absolute physical mastery, discipline, and combat readiness", weights: { red: 3, orange: 1 } },
      ],
    },

    // === BLOCK 2: Instincts Under Pressure (Q7-12) ===
    {
      id: 7,
      text: "You witness an injustice in public. Others are looking away. What's your FIRST instinct?",
      category: "Instinct",
      answers: [
        { text: "Pray for guidance, then approach the situation with spiritual authority", weights: { white: 3 } },
        { text: "Document everything, gather evidence, report it to the right people", weights: { black: 2, blue: 2 } },
        { text: "Physically position myself between the harm and the victim", weights: { red: 2, orange: 2 } },
        { text: "De-escalate with words, presence, and empathy", weights: { pink: 2, green: 2 } },
        { text: "Call it out loudly so everyone present is forced to acknowledge what's happening", weights: { orange: 3, blue: 1 } },
      ],
    },
    {
      id: 8,
      text: "Your team is falling apart. Morale is gone. Deadline is tomorrow. What do you do?",
      category: "Leadership Under Pressure",
      answers: [
        { text: "Gather everyone and speak from the heart about why this matters", weights: { white: 2, pink: 2 } },
        { text: "Quietly identify the real blocker, remove it, and tell no one what I did", weights: { black: 3 } },
        { text: "Take on the hardest task myself and lead by visible example", weights: { red: 2, orange: 2 } },
        { text: "Reorganize the plan — cut scope, reassign, find the fastest path to done", weights: { yellow: 2, blue: 2 } },
        { text: "Make sure everyone eats, rests for 20 minutes, then refocus as humans first", weights: { green: 2, pink: 2 } },
      ],
    },
    {
      id: 9,
      text: "You discover a friend has been lying to you for months. What hurts most?",
      category: "Emotional Processing",
      answers: [
        { text: "That they didn't trust me enough to tell me the truth", weights: { white: 2, pink: 2 } },
        { text: "That I didn't see it sooner — I should have noticed the signs", weights: { black: 3, purple: 1 } },
        { text: "That the foundation I thought we had was built on something rotten", weights: { brown: 3, blue: 1 } },
        { text: "That they chose deception over honest difficulty — cowardice over courage", weights: { orange: 2, red: 2 } },
        { text: "That I now have to rebuild my model of who they are from scratch", weights: { purple: 3 } },
      ],
    },
    {
      id: 10,
      text: "You have one hour to prepare for an unknown challenge. What do you do with that hour?",
      category: "Preparation Style",
      answers: [
        { text: "Meditate. Center myself. Trust that clarity will come when it's needed.", weights: { white: 3 } },
        { text: "Gather intelligence. Who, what, where, when, why. Information is armor.", weights: { black: 3, purple: 1 } },
        { text: "Check my gear. Make sure my tools are ready and my environment is set.", weights: { brown: 2, red: 2 } },
        { text: "Study. Read everything available. Understand the problem space.", weights: { purple: 3, yellow: 1 } },
        { text: "Warm up. Stretch. Shadow-box. Get my body and mind in sync.", weights: { red: 3, orange: 1 } },
      ],
    },
    {
      id: 11,
      text: "You can save one thing from a burning building (people are already safe). What do you grab?",
      category: "What You Value",
      answers: [
        { text: "A sacred text, a prayer book, or a meaningful religious artifact", weights: { white: 3 } },
        { text: "A hard drive with irreplaceable intelligence or records", weights: { black: 2, yellow: 2 } },
        { text: "Seeds, livestock records, or the family land deed", weights: { brown: 3 } },
        { text: "A research journal, an unfinished manuscript, or years of notes", weights: { purple: 3 } },
        { text: "A musical instrument, a painting, or a handwritten letter from someone I love", weights: { pink: 3 } },
      ],
    },
    {
      id: 12,
      text: "A stranger asks for help. You're busy, tired, and late. What tips the scale toward helping?",
      category: "Compassion Trigger",
      answers: [
        { text: "If I sense they're carrying spiritual weight — grief, guilt, despair", weights: { white: 3, pink: 1 } },
        { text: "If helping them also gathers useful information or builds a useful connection", weights: { black: 3 } },
        { text: "If they clearly need something practical — food, directions, a hand with a physical task", weights: { brown: 2, orange: 2 } },
        { text: "If their problem is interesting and I might learn something by solving it", weights: { purple: 2, yellow: 2 } },
        { text: "If they look scared. Fear in another person bypasses all my self-interest.", weights: { orange: 2, pink: 2 } },
      ],
    },

    // === BLOCK 3: Growth & Aspiration (Q13-18) ===
    {
      id: 13,
      text: "What kind of teacher would you be?",
      category: "How You Grow Others",
      answers: [
        { text: "The spiritual mentor — I help people find their purpose and meaning", weights: { white: 3, green: 1 } },
        { text: "The strategist — I teach people to think three moves ahead in any situation", weights: { black: 3, blue: 1 } },
        { text: "The master tradesperson — I teach by doing, side by side, hands dirty", weights: { brown: 2, red: 2 } },
        { text: "The professor — rigorous, demanding, but deeply committed to their understanding", weights: { purple: 3, yellow: 1 } },
        { text: "The coach — I build people up emotionally and push them past what they think is possible", weights: { green: 2, orange: 2 } },
      ],
    },
    {
      id: 14,
      text: "What does 'power' mean to you?",
      category: "Philosophy of Power",
      answers: [
        { text: "Spiritual authority — the power to heal, bless, and speak truth that transforms", weights: { white: 3 } },
        { text: "Information — the power to know what others don't and use it wisely", weights: { black: 3, purple: 1 } },
        { text: "Sustainability — the power to build things that endure beyond my lifetime", weights: { brown: 2, green: 2 } },
        { text: "Innovation — the power to create solutions that didn't exist before", weights: { yellow: 3, purple: 1 } },
        { text: "Physical capability — the power to protect, to act, to be unstoppable when it matters", weights: { red: 3, orange: 1 } },
      ],
    },
    {
      id: 15,
      text: "You're building your dream organization. What's its mission?",
      category: "What You'd Build",
      answers: [
        { text: "Healing intergenerational trauma and restoring spiritual wholeness to families", weights: { white: 2, pink: 2, green: 1 } },
        { text: "Protecting vulnerable people from threats they can't see or understand", weights: { black: 2, blue: 2 } },
        { text: "Preserving heritage, land rights, and the connection between people and place", weights: { brown: 3, green: 1 } },
        { text: "Advancing human knowledge and making it freely accessible to everyone", weights: { purple: 2, yellow: 2 } },
        { text: "Training the next generation of leaders who lead from the front", weights: { red: 2, orange: 2 } },
      ],
    },
    {
      id: 16,
      text: "What's your relationship with rules and systems?",
      category: "Order vs. Chaos",
      answers: [
        { text: "Rules matter, but divine law supersedes human law when they conflict", weights: { white: 2, blue: 1 } },
        { text: "I understand rules well enough to know when breaking them serves a higher purpose", weights: { black: 3 } },
        { text: "Good rules grow naturally from good practice. I trust tradition over theory.", weights: { brown: 3, blue: 1 } },
        { text: "Rules should be based on evidence and logic. Bad rules should be replaced, not obeyed.", weights: { purple: 2, yellow: 2 } },
        { text: "Rules are for the situations you can predict. Real life needs people who can improvise.", weights: { orange: 3, red: 1 } },
      ],
    },
    {
      id: 17,
      text: "Which historical or fictional figure do you most admire?",
      category: "Role Model",
      answers: [
        { text: "Someone like Mother Teresa, Martin Luther King Jr., or the Dalai Lama — spiritual leaders who changed the world through love", weights: { white: 2, pink: 2 } },
        { text: "Someone like Sun Tzu, Sherlock Holmes, or Machiavelli — brilliant strategic minds", weights: { black: 3, purple: 1 } },
        { text: "Someone like George Washington Carver, Wangari Maathai, or a family elder who kept everything together", weights: { brown: 3, green: 1 } },
        { text: "Someone like Da Vinci, Tesla, Marie Curie, or Hawking — minds that revealed hidden truths", weights: { purple: 2, yellow: 2 } },
        { text: "Someone like Bruce Lee, Miyamoto Musashi, or Teddy Roosevelt — people of decisive action", weights: { red: 2, orange: 2 } },
      ],
    },
    {
      id: 18,
      text: "How do you feel about vulnerability?",
      category: "Emotional Architecture",
      answers: [
        { text: "It's sacred. Being vulnerable is how we connect to the divine and to each other.", weights: { white: 2, pink: 2 } },
        { text: "It's a calculated risk. I choose when and with whom to be vulnerable.", weights: { black: 3 } },
        { text: "It's natural. A seed has to break open to grow. So do people.", weights: { green: 3, brown: 1 } },
        { text: "It's uncomfortable but necessary. Growth requires honest self-assessment.", weights: { purple: 2, blue: 2 } },
        { text: "It's a form of courage. Showing your real self when it's hard is the bravest thing there is.", weights: { orange: 2, pink: 2 } },
      ],
    },

    // === BLOCK 4: Skill & Domain (Q19-24) ===
    {
      id: 19,
      text: "If you had unlimited funding and five years, what would you study?",
      category: "Intellectual Gravity",
      answers: [
        { text: "Theology, comparative religion, philosophy of consciousness", weights: { white: 3, purple: 1 } },
        { text: "Intelligence studies, cryptography, behavioral psychology", weights: { black: 3, blue: 1 } },
        { text: "Agriculture, ecology, veterinary science, land management", weights: { brown: 3 } },
        { text: "Pure mathematics, physics, computer science, cognitive science", weights: { yellow: 3, purple: 1 } },
        { text: "Music, visual art, narrative design, human psychology of emotion", weights: { pink: 3, green: 1 } },
      ],
    },
    {
      id: 20,
      text: "You enter a room full of people you don't know. What do you naturally do?",
      category: "Social Instinct",
      answers: [
        { text: "Find the one person who looks like they're struggling and quietly check on them", weights: { white: 2, pink: 2 } },
        { text: "Observe from the edges. Map the social dynamics before engaging.", weights: { black: 3 } },
        { text: "Find a practical task — help with setup, fix something, make myself useful", weights: { brown: 2, blue: 2 } },
        { text: "Gravitate toward whoever is having the most interesting conversation", weights: { purple: 2, yellow: 2 } },
        { text: "Introduce myself boldly. If I'm here, I might as well make it count.", weights: { orange: 2, red: 2 } },
      ],
    },
    {
      id: 21,
      text: "What skill do people MOST often come to you for help with?",
      category: "Natural Authority",
      answers: [
        { text: "Emotional support, spiritual guidance, or help making sense of suffering", weights: { white: 2, pink: 2 } },
        { text: "Strategy, planning, or figuring out what's really going on in a complex situation", weights: { black: 2, blue: 2 } },
        { text: "Practical help — fixing things, building things, getting things done with their hands", weights: { brown: 2, red: 2 } },
        { text: "Research, analysis, explaining complicated things in simple terms", weights: { purple: 2, yellow: 2 } },
        { text: "Motivation, confidence-building, or getting them to take action they've been avoiding", weights: { orange: 2, green: 2 } },
      ],
    },
    {
      id: 22,
      text: "You're leading a project. What's your leadership style?",
      category: "Leadership",
      answers: [
        { text: "Servant leadership — I exist to help my team succeed, not to command them", weights: { white: 2, green: 2 } },
        { text: "Strategic oversight — I set the board, define the win condition, delegate execution", weights: { black: 2, blue: 2 } },
        { text: "Steady hand — I keep things grounded, practical, and moving forward daily", weights: { brown: 2, yellow: 2 } },
        { text: "Visionary — I paint the picture of what we're building and inspire people toward it", weights: { purple: 2, pink: 2 } },
        { text: "Lead from the front — I do the hardest task first and the team follows", weights: { red: 2, orange: 2 } },
      ],
    },
    {
      id: 23,
      text: "What does success look like to you in 10 years?",
      category: "Vision",
      answers: [
        { text: "A community of people who found their purpose because I helped them look", weights: { white: 2, green: 2 } },
        { text: "A legacy of problems solved that most people never knew existed", weights: { black: 3, blue: 1 } },
        { text: "Healthy land, healthy family, healthy animals, and a place worth passing down", weights: { brown: 3 } },
        { text: "A body of work — published, built, or invented — that advances human knowledge", weights: { purple: 2, yellow: 2 } },
        { text: "A reputation for being someone who showed up when it mattered most", weights: { orange: 2, red: 2 } },
      ],
    },
    {
      id: 24,
      text: "Which work environment makes you most effective?",
      category: "Habitat",
      answers: [
        { text: "A sanctuary — quiet, sacred, with room for reflection and depth", weights: { white: 3, purple: 1 } },
        { text: "A secure operations center — screens, data, controlled access", weights: { black: 2, yellow: 2 } },
        { text: "Outdoors or in a workshop — space to move, build, and work with my hands", weights: { brown: 2, red: 2 } },
        { text: "A library or lab — surrounded by knowledge and the tools to explore it", weights: { purple: 3 } },
        { text: "A stage, studio, or open floor — collaborative, energetic, expressive", weights: { pink: 2, orange: 2 } },
      ],
    },

    // === BLOCK 5: The Deep Questions (Q25-30) ===
    {
      id: 25,
      text: "What would you sacrifice everything for?",
      category: "Ultimate Conviction",
      answers: [
        { text: "The truth of something I know in my soul to be sacred", weights: { white: 3, purple: 1 } },
        { text: "Protecting someone who cannot protect themselves, even if no one ever knows", weights: { black: 2, blue: 2 } },
        { text: "The land, the family, the community — the things that root us to this world", weights: { brown: 3, green: 1 } },
        { text: "A breakthrough that could change how humanity understands itself", weights: { purple: 2, yellow: 2 } },
        { text: "Someone I love. Without hesitation.", weights: { pink: 3, red: 1 } },
      ],
    },
    {
      id: 26,
      text: "You're given the power to change ONE thing about how the world works. What changes?",
      category: "World Vision",
      answers: [
        { text: "Every person can feel, unmistakably, that they are loved and that their life has meaning", weights: { white: 3, pink: 1 } },
        { text: "No one in power can hide what they're really doing. Total transparency of authority.", weights: { black: 3, blue: 1 } },
        { text: "Every family has a home, clean water, and enough food. The basics are guaranteed.", weights: { brown: 2, green: 2 } },
        { text: "Education is free, universal, and actually teaches people how to think", weights: { purple: 2, yellow: 2 } },
        { text: "No one is afraid to do the right thing. Moral courage becomes the default.", weights: { orange: 3, red: 1 } },
      ],
    },
    {
      id: 27,
      text: "How do you want to be remembered?",
      category: "Legacy",
      answers: [
        { text: "As someone who helped people find peace, purpose, and connection to something eternal", weights: { white: 3, pink: 1 } },
        { text: "As someone who saw what others missed and used it to protect the vulnerable", weights: { black: 3, blue: 1 } },
        { text: "As someone who built something lasting and left things better than they found them", weights: { brown: 2, green: 2 } },
        { text: "As someone who expanded what humanity knows and understood", weights: { purple: 2, yellow: 2 } },
        { text: "As someone who never backed down when it mattered", weights: { red: 2, orange: 2 } },
      ],
    },
    {
      id: 28,
      text: "When you pray, meditate, or have a quiet moment of reflection — what do you ask for?",
      category: "Inner Voice",
      answers: [
        { text: "Wisdom to understand the divine plan and courage to serve it", weights: { white: 3 } },
        { text: "Clarity to see what's really happening and strength to respond correctly", weights: { black: 2, blue: 2 } },
        { text: "Patience and endurance to keep going, keep growing, keep building", weights: { brown: 2, green: 2 } },
        { text: "Insight — a flash of understanding that illuminates what I've been missing", weights: { purple: 3, yellow: 1 } },
        { text: "The ability to be present with the people I love and make them feel safe", weights: { pink: 3, green: 1 } },
      ],
    },
    {
      id: 29,
      text: "You're standing at a crossroads. One path is safe but unfulfilling. The other is dangerous but meaningful. What guides your choice?",
      category: "Decision Engine",
      answers: [
        { text: "Faith. I choose the path I believe I was called to walk, regardless of the cost.", weights: { white: 3, orange: 1 } },
        { text: "Analysis. I assess the actual risk, not the perceived risk, and choose based on data.", weights: { black: 2, purple: 2 } },
        { text: "Roots. What choice honors the people who came before me and protects those who come after?", weights: { brown: 3 } },
        { text: "Curiosity. The meaningful path will teach me more, even if it hurts.", weights: { purple: 2, yellow: 2 } },
        { text: "Gut. My body knows before my mind does. I trust the instinct and move.", weights: { red: 2, orange: 2 } },
      ],
    },
    {
      id: 30,
      text: "Finally: if your heart could sing one note — one pure, sustained tone that expressed everything you are — what would it sound like?",
      category: "Heart Song",
      answers: [
        { text: "A cathedral bell. Deep, resonant, calling people home to something sacred.", weights: { white: 3, pink: 1 } },
        { text: "A minor chord in a dark room. Beautiful, complex, understood by few.", weights: { black: 3, purple: 1 } },
        { text: "A deep drum. Steady, earthen, the heartbeat of the land itself.", weights: { brown: 3 } },
        { text: "A crystalline frequency. Pure, precise, the sound of understanding.", weights: { purple: 2, yellow: 2 } },
        { text: "A war horn. Clear, unstoppable, felt in the chest before the ears register it.", weights: { red: 2, orange: 2 } },
        { text: "A lullaby. Soft, safe, the sound that tells someone everything will be okay.", weights: { pink: 3, green: 1 } },
      ],
    },
  ],

  // ─── ORDER EXPLANATIONS ───
  // Why each order was recommended, keyed by order id
  explanations: {
    white: {
      title: "The Radiant Covenant",
      oneFinger: "Your One Finger is Faith.",
      summary: "Your answers consistently pointed toward the sacred, the transcendent, and the healing of souls. You process the world through a spiritual lens first — not as escape from reality, but as the deepest engagement with it. When others see chaos, you sense purpose. When others feel despair, you carry hope as a tangible force.",
      why: "The Order of the White chose you because your Heart Song resonates at the frequency of devotion. Your quiz revealed a pattern: you are drawn to prayer over planning, to spiritual authority over institutional authority, and to healing the invisible wounds that no one else can see. This is the rarest and most demanding path — pure spiritual conviction as a way of life. Not as performance, but as identity.",
      leeQuote: "Bruce Lee's One Finger for you: the punch that lands without being thrown. Your presence changes rooms. Your faith changes people. That is your weapon.",
    },
    black: {
      title: "The Obsidian Accord",
      oneFinger: "Your One Finger is Vigilance.",
      summary: "Your answers revealed a mind that naturally operates in the spaces between — seeing patterns, detecting deception, and working in the margins so others can walk in the light. You're not dark; you're the one who understands the dark well enough to guard against it.",
      why: "The Order of the Black chose you because your Heart Song resonates at the frequency of hidden protection. Your quiz showed a consistent pattern: you observe before acting, you value information as the ultimate resource, and you're drawn to solving problems nobody else has even identified. This isn't paranoia — it's the deepest form of caring. You protect people from threats they never know about.",
      leeQuote: "Bruce Lee's One Finger for you: the strike that ends the fight before it begins. You don't need credit. You need results. That is your weapon.",
    },
    brown: {
      title: "The Earthen Compact",
      oneFinger: "Your One Finger is Stewardship.",
      summary: "Your answers revealed someone rooted to the earth — literally and figuratively. You think in generations, not quarters. You value what grows slowly and lasts long. Heritage, land, animals, family, community — these aren't abstract concepts to you. They're what you'd die for.",
      why: "The Order of the Brown chose you because your Heart Song resonates at the frequency of the land itself. Your quiz showed a pattern of practicality married to profound care: you restore before you replace, you build before you theorize, and you trust tradition earned through centuries of practice. In a world that moves too fast, you are the anchor.",
      leeQuote: "Bruce Lee's One Finger for you: the stance that cannot be moved. Roots go deeper than any attack can reach. That is your weapon.",
    },
    purple: {
      title: "The Arcane Throne",
      oneFinger: "Your One Finger is Understanding.",
      summary: "Your answers revealed a mind consumed by the need to know — not for power, not for status, but because understanding itself is sacred to you. Where others see a closed book, you see an unopened door. You believe ignorance is the root of most suffering.",
      why: "The Order of the Purple chose you because your Heart Song resonates at the frequency of truth itself. Your quiz showed a relentless pattern of curiosity, analysis, and the conviction that knowledge is the highest form of service. You're not an ivory tower intellectual — you're a scholar with a sword, someone who believes that understanding the problem IS solving the problem.",
      leeQuote: "Bruce Lee's One Finger for you: 'Absorb what is useful, discard what is not, add what is uniquely your own.' Knowledge refined through practice. That is your weapon.",
    },
    blue: {
      title: "The Azure Shield",
      oneFinger: "Your One Finger is Order.",
      summary: "Your answers revealed someone who believes in systems, structures, and the rule of law — not blindly, but because you understand that civilization itself depends on someone holding the line. You serve institutions because institutions serve people.",
      why: "The Order of the Blue chose you because your Heart Song resonates at the frequency of civic duty. Your quiz showed a pattern of structured thinking, institutional loyalty, and the belief that the best protection comes from strong systems fairly enforced. You're the backbone — the person who makes sure the rules work for everyone, not just the powerful.",
      leeQuote: "Bruce Lee's One Finger for you: the disciplined form executed ten thousand times. Mastery through repetition, service through structure. That is your weapon.",
    },
    green: {
      title: "The Verdant Path",
      oneFinger: "Your One Finger is Growth.",
      summary: "Your answers revealed someone who sees potential everywhere — in people, in organizations, in broken systems — and instinctively moves to nurture it. You believe stagnation is the only true death, and renewal is always possible.",
      why: "The Order of the Green chose you because your Heart Song resonates at the frequency of life itself. Your quiz showed a pattern of mentorship, restoration, and the deep conviction that every person and every system can be healed if someone cares enough to invest in them. You're not passive — growth requires fierce advocacy and relentless patience.",
      leeQuote: "Bruce Lee's One Finger for you: water. 'Be water, my friend.' You adapt, you nourish, you erode every obstacle through patient persistence. That is your weapon.",
    },
    yellow: {
      title: "The Solar Decree",
      oneFinger: "Your One Finger is Innovation.",
      summary: "Your answers revealed a builder, a maker, someone who looks at a problem and immediately starts designing the solution. Technology, systems, architecture — you believe the tools we build define the world we live in.",
      why: "The Order of the Yellow chose you because your Heart Song resonates at the frequency of creation. Your quiz showed a pattern of engineering thinking, practical problem-solving, and the conviction that the best way to predict the future is to build it. You don't just study the problem — you forge the solution and deploy it.",
      leeQuote: "Bruce Lee's One Finger for you: the jeet kune do intercepting fist — don't wait for the problem to arrive; build the solution before the problem materializes. That is your weapon.",
    },
    orange: {
      title: "The Ember Pact",
      oneFinger: "Your One Finger is Courage.",
      summary: "Your answers revealed someone who moves TOWARD danger while others retreat. Not recklessly, but because you understand that hesitation has a body count. When action is needed, you act. Period.",
      why: "The Order of the Orange chose you because your Heart Song resonates at the frequency of fire. Your quiz showed a consistent pattern: you value courage over comfort, action over deliberation, and direct engagement over cautious observation. This isn't impulsiveness — it's the trained instinct to respond when every second counts.",
      leeQuote: "Bruce Lee's One Finger for you: the fastest punch in martial arts. Speed, commitment, and total presence in the moment of action. That is your weapon.",
    },
    red: {
      title: "The Crimson Mandate",
      oneFinger: "Your One Finger is Discipline.",
      summary: "Your answers revealed a warrior at your core — someone who believes that physical mastery, martial discipline, and the willingness to stand in harm's way are the purest forms of service. Your body is your oath made manifest.",
      why: "The Order of the Red chose you because your Heart Song resonates at the frequency of steel. Your quiz showed a pattern of physical-first thinking, competitive excellence, and the deep understanding that sometimes the only thing standing between harm and the innocent is a person willing to absorb the blow. This is not violence — it is the last line of defense, perfected.",
      leeQuote: "Bruce Lee's One Finger for you: 'I am not teaching you anything. I just help you to explore yourself.' Your body is the instrument. Mastery is the practice. Defense of others is the purpose. That is your weapon.",
    },
    pink: {
      title: "The Rose Communion",
      oneFinger: "Your One Finger is Love.",
      summary: "Your answers revealed someone whose deepest power comes from empathy, beauty, and connection. You believe love is not weakness — it is the most terrifying force in the universe. You create safe spaces. You heal through presence. You fight with compassion as your weapon.",
      why: "The Order of the Pink chose you because your Heart Song resonates at the frequency of the heart itself. Your quiz showed a pattern of empathic instinct, creative expression, and the conviction that the ultimate battlefield is the human heart. You're not soft — you're the person who holds space when everyone else has fled from the emotional difficulty.",
      leeQuote: "Bruce Lee's One Finger for you: the open hand. Sometimes the most powerful technique is not the fist, but the hand that reaches out. That is your weapon.",
    },
  },
};

// ═══════════════════════════════════════════════════════════════
// QUIZ ENGINE
// ═══════════════════════════════════════════════════════════════

class HeartSongEngine {
  constructor(containerId, onComplete) {
    this.container = document.getElementById(containerId);
    this.onComplete = onComplete; // callback(orderId) when quiz completes
    this.currentQ = 0;
    this.answers = [];
    this.scores = {};
    // Initialize all order scores to 0
    ['white','black','brown','purple','blue','green','yellow','orange','red','pink'].forEach(o => this.scores[o] = 0);
  }

  start() {
    this.currentQ = 0;
    this.answers = [];
    Object.keys(this.scores).forEach(k => this.scores[k] = 0);
    this.renderIntro();
  }

  renderIntro() {
    const q = HEART_SONG_QUIZ;
    this.container.innerHTML = `
      <div class="quiz-intro">
        <div class="quiz-header">
          <div class="quiz-icon">🎵</div>
          <h2 class="quiz-title">${q.intro.title}</h2>
          <div class="quiz-subtitle">${q.intro.subtitle}</div>
        </div>

        <div class="quiz-philosophy">
          <p>${q.intro.philosophy}</p>
        </div>

        <div class="quiz-preacher-note">
          <div class="quiz-note-header">
            <span class="quiz-note-icon">✝</span>
            <span>A Note from The Real Preacher</span>
          </div>
          <p>${q.intro.preacher_note}</p>
        </div>

        <div class="quiz-instructions">
          <h4>How This Works</h4>
          <p>${q.intro.instructions}</p>
          <p class="quiz-disclaimer">${q.intro.disclaimer}</p>
        </div>

        <div class="quiz-start-wrap">
          <button class="btn-primary quiz-start-btn" onclick="heartSongQuiz.renderQuestion()">
            Begin the Heart Song Quiz →
          </button>
          <div class="quiz-skip">
            <button class="btn btn-back" onclick="heartSongQuiz.skip()">
              I already know my Order — skip to Builder →
            </button>
          </div>
        </div>
      </div>
    `;
  }

  renderQuestion() {
    if (this.currentQ >= HEART_SONG_QUIZ.questions.length) {
      this.renderResults();
      return;
    }

    const q = HEART_SONG_QUIZ.questions[this.currentQ];
    const total = HEART_SONG_QUIZ.questions.length;
    const pct = Math.round((this.currentQ / total) * 100);

    this.container.innerHTML = `
      <div class="quiz-question">
        <div class="quiz-progress">
          <div class="quiz-progress-bar">
            <div class="quiz-progress-fill" style="width:${pct}%"></div>
          </div>
          <div class="quiz-progress-text">
            <span>Question ${this.currentQ + 1} of ${total}</span>
            <span class="quiz-category">${q.category}</span>
          </div>
        </div>

        <div class="quiz-q-text">${q.text}</div>

        <div class="quiz-answers">
          ${q.answers.map((a, i) => `
            <button class="quiz-answer" onclick="heartSongQuiz.selectAnswer(${i})">
              <span class="quiz-answer-marker">${String.fromCharCode(65 + i)}</span>
              <span class="quiz-answer-text">${a.text}</span>
            </button>
          `).join('')}
        </div>

        ${this.currentQ > 0 ? `
          <div class="quiz-back-wrap">
            <button class="btn btn-back" onclick="heartSongQuiz.goBack()">← Previous Question</button>
          </div>
        ` : ''}
      </div>
    `;
  }

  selectAnswer(answerIndex) {
    const q = HEART_SONG_QUIZ.questions[this.currentQ];
    const answer = q.answers[answerIndex];

    // Store answer
    this.answers[this.currentQ] = answerIndex;

    // Apply weights
    for (const [orderId, points] of Object.entries(answer.weights)) {
      this.scores[orderId] = (this.scores[orderId] || 0) + points;
    }

    // Animate selection
    const buttons = this.container.querySelectorAll('.quiz-answer');
    buttons.forEach((btn, i) => {
      if (i === answerIndex) {
        btn.classList.add('selected');
      } else {
        btn.classList.add('faded');
      }
    });

    // Advance after brief delay
    setTimeout(() => {
      this.currentQ++;
      this.renderQuestion();
    }, 400);
  }

  goBack() {
    if (this.currentQ <= 0) return;

    // Undo last answer's weights
    const lastQ = HEART_SONG_QUIZ.questions[this.currentQ - 1];
    const lastAnswerIdx = this.answers[this.currentQ - 1];
    if (lastAnswerIdx !== undefined) {
      const lastAnswer = lastQ.answers[lastAnswerIdx];
      for (const [orderId, points] of Object.entries(lastAnswer.weights)) {
        this.scores[orderId] = Math.max(0, (this.scores[orderId] || 0) - points);
      }
    }

    this.currentQ--;
    this.renderQuestion();
  }

  renderResults() {
    // Sort orders by score
    const sorted = Object.entries(this.scores)
      .sort((a, b) => b[1] - a[1]);

    const topOrder = sorted[0][0];
    const topScore = sorted[0][1];
    const totalPoints = sorted.reduce((s, [_, v]) => s + v, 0);
    const topPct = totalPoints > 0 ? Math.round((topScore / totalPoints) * 100) : 0;

    const explanation = HEART_SONG_QUIZ.explanations[topOrder];
    const secondOrder = sorted[1][0];
    const secondExpl = HEART_SONG_QUIZ.explanations[secondOrder];

    // Get order data from GD if available
    const orderData = (typeof GD !== 'undefined' && GD.orders)
      ? GD.orders.find(o => o.id === topOrder)
      : { name: `Order of the ${topOrder.charAt(0).toUpperCase() + topOrder.slice(1)}`, color_hex: '#c8961a', spectrum: '' };

    const secondData = (typeof GD !== 'undefined' && GD.orders)
      ? GD.orders.find(o => o.id === secondOrder)
      : { name: `Order of the ${secondOrder.charAt(0).toUpperCase() + secondOrder.slice(1)}`, color_hex: '#999' };

    this.container.innerHTML = `
      <div class="quiz-results">
        <div class="quiz-result-header">
          <div class="quiz-result-icon">🎵</div>
          <h2 class="quiz-result-title">Your Heart Song Has Been Heard</h2>
        </div>

        <div class="quiz-result-order" style="border-color:${orderData.color_hex}">
          <div class="quiz-result-order-bar" style="background:${orderData.color_hex}"></div>
          <div class="quiz-result-badge" style="color:${orderData.color_hex}">${orderData.name}</div>
          <div class="quiz-result-covenant">${explanation.title}</div>
          <div class="quiz-result-finger">${explanation.oneFinger}</div>
          <div class="quiz-result-pct">${topPct}% alignment</div>
        </div>

        <div class="quiz-result-section">
          <h3>What Your Answers Revealed</h3>
          <p>${explanation.summary}</p>
        </div>

        <div class="quiz-result-section">
          <h3>Why This Order Chose You</h3>
          <p>${explanation.why}</p>
        </div>

        <div class="quiz-result-bruce">
          <div class="quiz-bruce-header">☯ The One Finger</div>
          <p>${explanation.leeQuote}</p>
        </div>

        ${sorted[1][1] > 0 ? `
          <div class="quiz-result-secondary">
            <h4>Your Secondary Resonance</h4>
            <div class="quiz-secondary-order" style="border-left-color:${secondData.color_hex}">
              <strong>${secondData.name}</strong> — ${secondExpl.oneFinger}
              <p style="margin-top:.25rem;font-size:.82rem;color:#5a5040">${secondExpl.summary.split('.')[0]}.</p>
            </div>
          </div>
        ` : ''}

        <div class="quiz-result-spectrum">
          <h4>Your Full Spectrum</h4>
          <div class="quiz-spectrum-bars">
            ${sorted.map(([id, score]) => {
              const od = (typeof GD !== 'undefined' && GD.orders) ? GD.orders.find(o=>o.id===id) : null;
              const color = od?.color_hex || '#999';
              const name = od?.name || id;
              const pct = totalPoints > 0 ? Math.round((score/totalPoints)*100) : 0;
              return `<div class="quiz-spectrum-row">
                <span class="quiz-spectrum-label">${name.replace('Order of the ','')}</span>
                <div class="quiz-spectrum-bar-wrap"><div class="quiz-spectrum-bar" style="width:${pct}%;background:${color}"></div></div>
                <span class="quiz-spectrum-pct">${pct}%</span>
              </div>`;
            }).join('')}
          </div>
        </div>

        <div class="quiz-preacher-note" style="margin-top:2rem">
          <div class="quiz-note-header"><span class="quiz-note-icon">⚜</span><span>Remember</span></div>
          <p>This recommendation is a mirror, not a cage. Your Heart Song pointed here — but you always have the freedom to choose a different Order. The quiz revealed your <em>natural resonance</em>. Your <em>intentional choice</em> is yours alone. First, choose yourself. Then make yourself worthy of being chosen.</p>
        </div>

        <div class="quiz-result-actions">
          <button class="btn-primary" onclick="heartSongQuiz.acceptOrder('${topOrder}')">
            Accept ${orderData.name} & Begin Building →
          </button>
          <button class="btn btn-back" onclick="heartSongQuiz.skip()" style="margin-top:.75rem;display:block">
            Choose a different Order →
          </button>
          <button class="btn btn-back" onclick="heartSongQuiz.start()" style="margin-top:.25rem;display:block;font-size:.75rem">
            Retake the Quiz
          </button>
        </div>
      </div>
    `;
  }

  acceptOrder(orderId) {
    if (this.onComplete) {
      this.onComplete(orderId);
    }
  }

  skip() {
    if (this.onComplete) {
      this.onComplete(null); // null means skip - go to wizard without pre-selection
    }
  }
}
