<?php
/**
 * ═══════════════════════════════════════════════════════════════
 * SECURITY GATE — DELETE THIS FILE AFTER RUNNING
 * This file performs database write operations.
 * It should not remain accessible on a production server.
 * ═══════════════════════════════════════════════════════════════
 */

// Refuse execution if a lockfile exists (already ran)
$lockFile = __DIR__ . '/.patch1.lock';
if (file_exists($lockFile)) {
    http_response_code(403);
    die('<h2>Patch already applied.</h2><p>Delete patches/patches_index.php from the server.</p>');
}

/**
 * ═══════════════════════════════════════════════════════════════
 * PALADIN PROFILE v5 — DB PATCH 1: Content Expansion (patches/patches_index.php)
 * LUMINOUS Engine · Repo-Wide Unique File: patches/patches_index.php
 * ═══════════════════════════════════════════════════════════════
 * 
 * WHAT THIS ADDS:
 *   +80 Callings       (8→16 per order, doubles available paths)
 *   +52 Specializations (new categories + deeper existing ones)
 *   +6  Attributes      (secondary/optional attributes)
 *   +2  Talent Trees    (Leadership & Craftsmanship, +30 nodes)
 *   +5  Bonus Feats     (between milestone feats)
 *   
 * BEFORE: 80 callings, 48 specs, 8 attrs, 4 trees/60 nodes, 10 feats
 * AFTER:  160 callings, 100 specs, 14 attrs, 6 trees/90 nodes, 15 feats
 *
 * USAGE:
 *   Web:  Visit https://your-domain.com/patches/ (DELETE after running)
 *   CLI:  php patches/patches_index.php
 *
 * SAFE TO RUN MULTIPLE TIMES (uses INSERT IGNORE / IF NOT EXISTS)
 * ═══════════════════════════════════════════════════════════════
 */

declare(strict_types=1);

// ─── BOOTSTRAP ───
$configPath = __DIR__ . '/../config.php';
if (!file_exists($configPath)) {
    // patches/ is always one level below root — no alternate path needed
    // $configPath already set correctly above
}
if (!file_exists($configPath)) {
    die("ERROR: config.php not found. Run the installer first.\n");
}
require_once $configPath;

$dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
$pdo = new PDO($dsn, DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$isWeb = php_sapi_name() !== 'cli';
$results = [];

function logResult(string $msg, string $level = 'ok'): void {
    global $results;
    $results[] = ['level' => $level, 'msg' => $msg];
}

function batchInsertCallings(PDO $pdo, array $rows): int {
    $stmt = $pdo->prepare(
        "INSERT IGNORE INTO `callings` (`order_id`, `name`, `description`) VALUES (?, ?, ?)"
    );
    $count = 0;
    foreach ($rows as $r) {
        $stmt->execute($r);
        if ($stmt->rowCount() > 0) $count++;
    }
    return $count;
}

function batchInsertSpecs(PDO $pdo, array $rows): int {
    $stmt = $pdo->prepare(
        "INSERT IGNORE INTO `specializations` (`category`, `name`, `description`) VALUES (?, ?, ?)"
    );
    $count = 0;
    foreach ($rows as $r) {
        $stmt->execute($r);
        if ($stmt->rowCount() > 0) $count++;
    }
    return $count;
}

// ═══════════════════════════════════════════════════════════════
// PATCH 1: NEW CALLINGS (+80, bringing each order to 16)
// ═══════════════════════════════════════════════════════════════

$newCallings = [
    // ── WHITE (8 new) ──
    ['white', 'Exorcist', 'Specialist in removing spiritual corruption, negative attachments, and inherited trauma patterns from individuals and spaces'],
    ['white', 'Vigil Keeper', 'Maintains sacred watch during transitions — birth, death, crisis, transformation. The one who holds space when the veil is thin'],
    ['white', 'Blessing Smith', 'Crafts personalized blessings, prayers, and sacred rituals for specific needs. Spiritual toolmaker'],
    ['white', 'Grief Walker', 'Accompanies others through loss, bereavement, and the reconstruction of meaning after devastation'],
    ['white', 'Radiant Scribe', 'Records sacred experiences, writes liturgy, preserves spiritual wisdom in written form for future generations'],
    ['white', 'Covenant Keeper', 'Specialist in vows, promises, and sacred agreements. Ensures oaths are honored and understood'],
    ['white', 'Sanctuary Warden', 'Protects and maintains holy spaces — physical and emotional — where healing and worship can occur safely'],
    ['white', 'Dawn Herald', 'Carries messages of hope into the darkest situations. First light after the longest night'],

    // ── BLACK (8 new) ──
    ['black', 'Cipher Knight', 'Master of encrypted communication, hidden messages, and secure information channels'],
    ['black', 'Wraith Hunter', 'Tracks and neutralizes threats that operate through deception, manipulation, and hidden influence'],
    ['black', 'Archive Keeper', 'Guards sensitive records, classified knowledge, and information that could be weaponized if exposed'],
    ['black', 'Ghost Protocol', 'Operates without attribution. Completes missions where plausible deniability is essential for the greater good'],
    ['black', 'Veil Walker', 'Moves between social worlds — high society and underground, corporate and street — gathering intelligence across boundaries'],
    ['black', 'Counter-Curse Specialist', 'Identifies and dismantles systemic corruption, institutional rot, and generational dysfunction patterns'],
    ['black', 'Obsidian Judge', 'Delivers consequences to those who escape conventional justice. The court of last resort'],
    ['black', 'Deep Cover Warden', 'Maintains long-term presence in hostile environments, reporting on threats from within enemy systems'],

    // ── BROWN (8 new) ──
    ['brown', 'Well Keeper', 'Guardian of water sources, both literal and metaphorical. Ensures communities have access to clean water and clean truth'],
    ['brown', 'Boundary Walker', 'Patrols the edges of communities and territories, maintaining borders and resolving disputes between neighbors'],
    ['brown', 'Seed Vault Guardian', 'Preserves genetic diversity, heirloom varieties, and traditional knowledge about food and agriculture'],
    ['brown', 'Storm Watcher', 'Monitors natural patterns, predicts disruptions, and prepares communities for environmental challenges'],
    ['brown', 'Hearth Tender', 'Maintains the communal fire — literally and figuratively. Ensures gathering places remain warm, fed, and functional'],
    ['brown', 'Trail Blazer', 'Opens new paths through wilderness, bureaucracy, or social obstacles. Makes the way passable for others to follow'],
    ['brown', 'Timber Warden', 'Manages forests and woodlands sustainably. Understands the balance between harvest and regeneration'],
    ['brown', 'Heritage Keeper', 'Preserves oral histories, family traditions, land records, and the intangible cultural inheritance of communities'],

    // ── PURPLE (8 new) ──
    ['purple', 'Theorem Knight', 'Attacks complex problems with mathematical rigor. Proves what others only suspect through formal logical methods'],
    ['purple', 'Pattern Breaker', 'Identifies hidden assumptions and false patterns that deceive everyone else. The scientist of deception'],
    ['purple', 'Library Warden', 'Protects archives, repositories, and knowledge stores from corruption, censorship, and deliberate destruction'],
    ['purple', 'Hypothesis Paladin', 'Lives in the space between what is known and unknown. Designs experiments that reveal new truths'],
    ['purple', 'Codex Master', 'Creates frameworks, taxonomies, and classification systems that organize chaotic information into actionable knowledge'],
    ['purple', 'Mind Fortress', 'Specialist in mental discipline, memory techniques, and cognitive defense against manipulation and propaganda'],
    ['purple', 'Translation Knight', 'Bridges knowledge domains. Takes expertise from one field and makes it useful in another'],
    ['purple', 'Axiom Guardian', 'Protects foundational truths from erosion by fashion, politics, or convenience. Defends first principles'],

    // ── BLUE (8 new) ──
    ['blue', 'Protocol Officer', 'Designs and enforces standard operating procedures that prevent catastrophe through consistent execution'],
    ['blue', 'Compliance Warden', 'Ensures organizations follow their own stated values and regulations. Internal accountability specialist'],
    ['blue', 'Dispatch Commander', 'Coordinates multi-team operations in real-time. The voice on the radio that keeps everyone alive'],
    ['blue', 'Evidence Keeper', 'Maintains chain of custody for proof, documentation, and records that must withstand legal scrutiny'],
    ['blue', 'Civil Engineer Paladin', 'Builds and maintains the physical infrastructure that civilized life depends on — roads, bridges, utilities'],
    ['blue', 'Watch Commander', 'Manages shift operations, ensuring 24/7 coverage of critical protective functions without burnout'],
    ['blue', 'Constitutional Guardian', 'Defends founding documents, charters, and institutional principles against erosion by expedience'],
    ['blue', 'Emergency Coordinator', 'Plans for disasters before they happen and coordinates multi-agency response when they do'],

    // ── GREEN (8 new) ──
    ['green', 'Compost Knight', 'Transforms waste, failure, and decay into fertile ground for new growth. The alchemist of second chances'],
    ['green', 'Watershed Guardian', 'Protects the systems that sustain communities — not just water, but supply chains, support networks, information flow'],
    ['green', 'Pollinator', 'Cross-fertilizes ideas between communities, industries, and disciplines. Creates unexpected connections that bear fruit'],
    ['green', 'Nursery Warden', 'Protects new ideas, new organizations, and new people during their most vulnerable growth phase'],
    ['green', 'Succession Planner', 'Ensures leadership transitions happen smoothly. Builds the next generation of leaders before they are needed'],
    ['green', 'Root Doctor', 'Diagnoses problems at their source, not their symptoms. Treats the cause, not the complaint'],
    ['green', 'Harvest Master', 'Knows when efforts are ready to be collected and distributed. Timing specialist for maximum community benefit'],
    ['green', 'Rewilding Knight', 'Restores natural systems, communities, and individuals to their self-sustaining state after periods of damage'],

    // ── YELLOW (8 new) ──
    ['yellow', 'Algorithm Knight', 'Designs the invisible decision-making systems that shape how technology serves (or fails) humanity'],
    ['yellow', 'Open Source Paladin', 'Builds tools and systems that belong to everyone. Fights proprietary lock-in and knowledge hoarding'],
    ['yellow', 'Data Shepherd', 'Protects personal data, ensures privacy, and fights surveillance overreach through technical countermeasures'],
    ['yellow', 'Prototype Warden', 'Rapidly builds working models that prove concepts. Turns "impossible" into "look, it works" within days'],
    ['yellow', 'Grid Keeper', 'Maintains critical infrastructure — power grids, networks, water treatment — that civilization depends on daily'],
    ['yellow', 'Accessibility Knight', 'Ensures technology serves everyone, including those with disabilities, limited resources, or low technical literacy'],
    ['yellow', 'Debug Paladin', 'Finds and fixes the hidden flaws in systems before they cause harm. The quality engineer of civilization'],
    ['yellow', 'Future Architect', 'Designs systems for conditions that don\'t exist yet. Plans for the world as it will be, not as it is'],

    // ── ORANGE (8 new) ──
    ['orange', 'Breach Specialist', 'First through the door in every crisis. Trained to open paths through obstacles others consider impassable'],
    ['orange', 'Smoke Jumper', 'Deploys into active disaster zones with minimal preparation. Thrives in chaos others cannot endure'],
    ['orange', 'Morale Officer', 'Maintains fighting spirit during sustained difficulty. Prevents despair from becoming contagious'],
    ['orange', 'Rescue Knight', 'Specializes in extracting people from dangerous situations — physical, emotional, institutional, or social'],
    ['orange', 'Flash Point', 'Identifies the exact moment when action becomes necessary and executes without hesitation'],
    ['orange', 'Endurance Specialist', 'Sustains high-intensity performance over periods that would break others. The marathon within the sprint'],
    ['orange', 'Catalyst Knight', 'Triggers necessary change that everyone knows is needed but no one has the courage to initiate'],
    ['orange', 'Signal Flare', 'Makes the invisible visible. Forces attention onto crises that are being ignored or suppressed'],

    // ── RED (8 new) ──
    ['red', 'Shield Master', 'Defensive combat specialist. Turns protection itself into an art form. The wall that fights back'],
    ['red', 'Armorer', 'Creates, maintains, and improves protective equipment — physical, digital, institutional. The forge behind the line'],
    ['red', 'Sparring Partner', 'Trains others by providing safe but genuine opposition. Makes people stronger without breaking them'],
    ['red', 'Tournament Champion', 'Competes in structured contests to demonstrate excellence and inspire others through visible achievement'],
    ['red', 'Siege Engineer', 'Specializes in overcoming fortified opposition through sustained, methodical pressure rather than brute force'],
    ['red', 'Scout Warrior', 'Combines reconnaissance with combat capability. Goes ahead, maps the danger, and fights through it'],
    ['red', 'Formation Leader', 'Coordinates group combat tactics. Turns individual fighters into a cohesive, multiplied force'],
    ['red', 'Honor Guard', 'Protects symbols, memorials, ceremonies, and the dignity of institutions through disciplined martial presence'],

    // ── PINK (8 new) ──
    ['pink', 'Lullaby Knight', 'Specializes in calming fear, soothing trauma, and creating the conditions for rest and recovery'],
    ['pink', 'Beauty Warden', 'Protects aesthetic heritage — architecture, landscapes, art, traditions — from destruction by neglect or progress'],
    ['pink', 'Festival Master', 'Organizes celebrations, rituals, and communal joy. Understands that communities need to celebrate to survive'],
    ['pink', 'Empathy Shield', 'Uses deep emotional understanding as a defensive tool. Prevents manipulation by understanding it completely'],
    ['pink', 'Color Keeper', 'Maintains vibrancy, creativity, and emotional range in communities that are being ground down by monotony or despair'],
    ['pink', 'Memory Weaver', 'Preserves personal and communal stories through creative retelling. Turns experience into narrative heritage'],
    ['pink', 'Harmony Knight', 'Resolves discord through creative synthesis. Finds the note that makes competing voices sound like a chord'],
    ['pink', 'Grace Warden', 'Embodies and teaches elegance under pressure. Maintains dignity and beauty even in desperate circumstances'],
];

$callingCount = batchInsertCallings($pdo, $newCallings);
logResult("Callings: +{$callingCount} new (of " . count($newCallings) . " attempted)");


// ═══════════════════════════════════════════════════════════════
// PATCH 2: NEW SPECIALIZATIONS (+52, across 8 new + 3 expanded categories)
// ═══════════════════════════════════════════════════════════════

$newSpecs = [
    // ── NEW CATEGORY: TECHNOLOGY ──
    ['TECHNOLOGY', 'Blockchain Architect', 'Distributed ledger systems, smart contracts, decentralized governance protocols'],
    ['TECHNOLOGY', 'Cloud Infrastructure Engineer', 'AWS/Azure/GCP architecture, containerization, serverless computing, reliability engineering'],
    ['TECHNOLOGY', 'UX/UI Designer', 'Human-centered design, interaction patterns, accessibility, design systems at scale'],
    ['TECHNOLOGY', 'DevSecOps Engineer', 'Security-integrated development pipelines, automated compliance, shift-left security'],
    ['TECHNOLOGY', 'Robotics Engineer', 'Autonomous systems, sensor fusion, human-robot interaction, industrial automation'],
    ['TECHNOLOGY', 'Network Architect', 'Enterprise networking, SDN, zero-trust architecture, telecommunications infrastructure'],

    // ── NEW CATEGORY: AGRICULTURE ──
    ['AGRICULTURE', 'Regenerative Farmer', 'Soil restoration, carbon sequestration, no-till methods, cover cropping, holistic land management'],
    ['AGRICULTURE', 'Apiarist', 'Beekeeping, pollinator conservation, honey production, colony health management'],
    ['AGRICULTURE', 'Aquaculture Specialist', 'Fish farming, sustainable seafood, marine ecosystem management, water quality'],
    ['AGRICULTURE', 'Viticulturist', 'Grape cultivation, vineyard management, terroir analysis, sustainable winemaking'],
    ['AGRICULTURE', 'Ranch Manager', 'Large-scale livestock operations, grazing rotation, breeding programs, ranch economics'],
    ['AGRICULTURE', 'Urban Farmer', 'Rooftop gardens, vertical farming, community food systems, food desert intervention'],

    // ── NEW CATEGORY: EMERGENCY SERVICES ──
    ['EMERGENCY SERVICES', 'Firefighter / HazMat', 'Structural firefighting, hazardous materials response, rescue operations'],
    ['EMERGENCY SERVICES', 'Search and Rescue', 'Wilderness SAR, urban disaster response, swift water rescue, technical rope rescue'],
    ['EMERGENCY SERVICES', 'Crisis Negotiator', 'Hostage negotiation, suicide intervention, de-escalation, tactical communication'],
    ['EMERGENCY SERVICES', 'Disaster Recovery Planner', 'Business continuity, community resilience, infrastructure restoration after catastrophe'],
    ['EMERGENCY SERVICES', 'Forensic Investigator', 'Crime scene analysis, digital forensics, evidence processing, expert court testimony'],

    // ── NEW CATEGORY: SOCIAL WORK ──
    ['SOCIAL WORK', 'Child Welfare Specialist', 'Foster care advocacy, abuse investigation, family reunification, child development'],
    ['SOCIAL WORK', 'Addiction Counselor', 'Substance abuse treatment, recovery support, harm reduction, relapse prevention'],
    ['SOCIAL WORK', 'Domestic Violence Advocate', 'Survivor support, safety planning, legal advocacy, shelter coordination'],
    ['SOCIAL WORK', 'Community Organizer', 'Grassroots mobilization, civic engagement, coalition building, neighborhood advocacy'],
    ['SOCIAL WORK', 'Refugee Resettlement Specialist', 'Immigration support, cultural integration, language services, trauma-informed relocation'],

    // ── NEW CATEGORY: COMMUNICATIONS ──
    ['COMMUNICATIONS', 'Investigative Journalist', 'Deep-dive reporting, source protection, accountability journalism, FOIA expertise'],
    ['COMMUNICATIONS', 'Public Relations Strategist', 'Reputation management, crisis communication, media relations, brand narrative'],
    ['COMMUNICATIONS', 'Technical Writer', 'Documentation, API references, user guides, knowledge base architecture'],
    ['COMMUNICATIONS', 'Podcast Producer', 'Audio storytelling, interview technique, audience development, syndication'],
    ['COMMUNICATIONS', 'Speech Writer', 'Executive communications, political rhetoric, motivational address, eulogy writing'],

    // ── NEW CATEGORY: ATHLETICS & FITNESS ──
    ['ATHLETICS', 'Martial Arts Instructor', 'Combat discipline, self-defense pedagogy, competition coaching, philosophical grounding'],
    ['ATHLETICS', 'Strength and Conditioning Coach', 'Athletic performance optimization, injury prevention, periodization programming'],
    ['ATHLETICS', 'Yoga / Meditation Teacher', 'Mindfulness instruction, breathwork, movement therapy, stress management'],
    ['ATHLETICS', 'Adventure Guide', 'Mountaineering, kayaking, wilderness survival, outdoor leadership, risk management'],
    ['ATHLETICS', 'Sports Medicine Specialist', 'Athletic injury treatment, rehabilitation, performance recovery, concussion protocols'],

    // ── EXPANDING EXISTING: STEM ──
    ['STEM', 'Bioinformatics Specialist', 'Computational biology, genomic data analysis, protein structure prediction, drug discovery pipelines'],
    ['STEM', 'Materials Scientist', 'Novel materials development, nanotechnology, composite engineering, sustainable materials'],
    ['STEM', 'Astrophysicist', 'Stellar dynamics, cosmology, exoplanet research, space mission design'],
    ['STEM', 'Epidemiologist', 'Disease tracking, outbreak response, statistical modeling, public health surveillance'],

    // ── EXPANDING EXISTING: CREATIVE ──
    ['CREATIVE', 'Graphic Novelist', 'Sequential art, visual storytelling, independent publishing, cultural commentary through comics'],
    ['CREATIVE', 'Voice Actor / Narrator', 'Character voice work, audiobook narration, vocal performance, dialect mastery'],
    ['CREATIVE', 'Choreographer', 'Movement design, dance direction, physical storytelling, embodied expression'],
    ['CREATIVE', 'Architect', 'Building design, sacred spaces, sustainable construction, community-centered architecture'],

    // ── EXPANDING EXISTING: TRADES ──
    ['TRADES', 'Plumber / Pipe Fitter', 'Water systems, gas lines, steam fitting, backflow prevention, sanitation infrastructure'],
    ['TRADES', 'Welder / Fabricator', 'Structural welding, artistic metalwork, pressure vessel certification, underwater welding'],
    ['TRADES', 'HVAC Technician', 'Climate control systems, refrigeration, indoor air quality, energy efficiency'],
    ['TRADES', 'Stone Mason', 'Traditional masonry, restoration, monument construction, heritage building repair'],
    ['TRADES', 'Farrier', 'Horseshoeing, equine hoof care, lameness assessment, traditional blacksmithing for livestock'],

    // ── NEW CATEGORY: GOVERNANCE ──
    ['GOVERNANCE', 'City Planner', 'Urban development, zoning, transportation planning, community engagement, smart city design'],
    ['GOVERNANCE', 'Election Administrator', 'Voter registration, ballot security, election logistics, democratic process protection'],
    ['GOVERNANCE', 'Tribal Affairs Specialist', 'Indigenous governance, treaty rights, cultural sovereignty, intergovernmental relations'],
    ['GOVERNANCE', 'Non-Profit Board Director', 'Fiduciary oversight, strategic governance, organizational accountability, mission alignment'],

    // ── NEW CATEGORY: TRANSPORTATION ──
    ['TRANSPORTATION', 'Commercial Pilot', 'Fixed-wing or rotary aircraft operation, cargo/passenger transport, instrument flight, emergency procedures'],
    ['TRANSPORTATION', 'Maritime Captain', 'Vessel operation, coastal and ocean navigation, crew management, port logistics'],
    ['TRANSPORTATION', 'Logistics Coordinator', 'Supply chain routing, fleet management, last-mile delivery optimization, warehouse operations'],
];

$specCount = batchInsertSpecs($pdo, $newSpecs);
logResult("Specializations: +{$specCount} new (of " . count($newSpecs) . " attempted)");


// ═══════════════════════════════════════════════════════════════
// PATCH 3: SECONDARY ATTRIBUTES (+6)
// ═══════════════════════════════════════════════════════════════

$newAttributes = [
    ['fth', 'Faith',       'Connection to the divine or transcendent. Power of conviction and spiritual resilience', 9, 0],
    ['hon', 'Honor',       'Reputation, integrity, trustworthiness. How strongly others believe your word', 10, 0],
    ['cre', 'Creativity',  'Imagination, innovation, ability to see novel solutions and make unexpected connections', 11, 0],
    ['res', 'Resolve',     'Determination under sustained pressure. Ability to endure prolonged difficulty without breaking', 12, 0],
    ['emp', 'Empathy',     'Emotional intelligence, ability to read and share the feelings of others accurately', 13, 0],
    ['ada', 'Adaptability','Flexibility in changing situations. Speed of adjustment when plans fail or environments shift', 14, 0],
];

$attrStmt = $pdo->prepare(
    "INSERT IGNORE INTO `attributes` (`id`, `name`, `description`, `sort_order`, `is_core`) VALUES (?, ?, ?, ?, ?)"
);
$attrCount = 0;
foreach ($newAttributes as $a) {
    $attrStmt->execute($a);
    if ($attrStmt->rowCount() > 0) $attrCount++;
}
logResult("Attributes: +{$attrCount} secondary attributes (of " . count($newAttributes) . " attempted)");


// ═══════════════════════════════════════════════════════════════
// PATCH 4: NEW TALENT TREES (+2 trees, +30 nodes)
// ═══════════════════════════════════════════════════════════════

// Add new talent trees
$treeStmt = $pdo->prepare(
    "INSERT IGNORE INTO `talent_trees` (`id`, `name`, `icon`, `description`, `sort_order`) VALUES (?, ?, ?, ?, ?)"
);

$treeStmt->execute(['leadership', 'Leadership / Command', '👑', 'The art of directing others, building organizations, and amplifying impact through collective action. From team lead to movement founder.', 5]);
$treeStmt->execute(['craft', 'Craftsmanship / Creation', '🔨', 'The mastery of making — physical, digital, artistic, or systemic. Building things that endure and serve beyond their creator.', 6]);

$treeCount = 0;
// Check if trees were actually inserted
$existingTrees = $pdo->query("SELECT id FROM talent_trees WHERE id IN ('leadership','craft')")->fetchAll(PDO::FETCH_COLUMN);
$treeCount = count($existingTrees);

$newNodes = [
    // ── LEADERSHIP TREE (15 tiers) ──
    ['leadership', 'Take Charge', 'Step into leadership naturally when a vacuum exists. Others accept your authority without it being formally granted', 0],
    ['leadership', 'Delegation', 'Assign tasks to the right people and trust them to execute. Resist the urge to do everything yourself', 1],
    ['leadership', 'Vision Casting', 'Articulate a compelling picture of where the group is going that makes people want to follow', 2],
    ['leadership', 'Accountability', 'Hold yourself and others to stated commitments without creating resentment or fear', 3],
    ['leadership', 'Conflict Resolution', 'Address interpersonal friction within teams before it becomes toxic. Turn disagreement into strength', 4],
    ['leadership', 'Resource Allocation', 'Distribute limited resources — time, money, attention, people — to maximize mission impact', 5],
    ['leadership', 'Strategic Planning', 'Think in timelines longer than the current crisis. Balance immediate needs with long-term objectives', 6],
    ['leadership', 'Talent Development', 'Identify potential in others and create conditions for them to grow into their capabilities', 7],
    ['leadership', 'Crisis Leadership', 'Maintain clear thinking and decisive action when everything is on fire and people are looking to you', 8],
    ['leadership', 'Coalition Building', 'Unite diverse groups with different interests around a shared mission. Politics as service', 9],
    ['leadership', 'Institutional Design', 'Create organizations, processes, and cultures that function well beyond your personal involvement', 10],
    ['leadership', 'Succession Planning', 'Develop the leaders who will replace you. Build something that outlives your direct participation', 11],
    ['leadership', 'Movement Leadership', 'Scale from leading a team to leading a movement. Inspire action in people you will never personally meet', 12],
    ['leadership', 'Servant Leadership Mastery', 'Lead by serving. Your authority comes entirely from your willingness to do the hardest work for others', 13],
    ['leadership', 'Legendary Commander', 'Your leadership decisions are studied by others. You have shaped how people think about leadership itself', 14],

    // ── CRAFTSMANSHIP TREE (15 tiers) ──
    ['craft', 'Tool Proficiency', 'Master the basic tools of your chosen craft — physical, digital, or conceptual', 0],
    ['craft', 'Material Knowledge', 'Understand the properties and behaviors of what you work with — wood, code, words, metal, fabric', 1],
    ['craft', 'Precision', 'Execute with accuracy. The gap between your intention and your output narrows toward zero', 2],
    ['craft', 'Design Thinking', 'Think about what you build from the perspective of who will use it, not just how to make it', 3],
    ['craft', 'Iteration', 'Improve through repeated cycles of create-test-refine. Embrace version 2 as better than version 1', 4],
    ['craft', 'Signature Style', 'Your work becomes recognizable. A distinctive approach emerges that is authentically yours', 5],
    ['craft', 'Repair Mastery', 'Fix what is broken rather than replacing it. Restoration as a discipline and a philosophy', 6],
    ['craft', 'Teaching the Craft', 'Transfer your skills to apprentices. Document your methods so they survive beyond your hands', 7],
    ['craft', 'Commission Work', 'Create to specification for others while maintaining your standards. Client work as sacred duty', 8],
    ['craft', 'Innovation', 'Push the boundaries of your craft. Develop new techniques, tools, or approaches that advance the field', 9],
    ['craft', 'Scale Production', 'Move from one-off creation to systems that produce quality consistently at higher volume', 10],
    ['craft', 'Quality Authority', 'Your judgment of quality in your domain is trusted as authoritative. Others bring work to you for assessment', 11],
    ['craft', 'Interdisciplinary Craft', 'Combine techniques from multiple domains to create work that could not exist within any single tradition', 12],
    ['craft', 'Living Legacy', 'Your creations are in active use by others. The things you built serve people daily', 13],
    ['craft', 'Master Craftsperson', 'Your work defines the standard in your field. You have made the craft itself better than you found it', 14],
];

$nodeStmt = $pdo->prepare(
    "INSERT IGNORE INTO `talent_nodes` (`tree_id`, `name`, `description`, `tier`) VALUES (?, ?, ?, ?)"
);
$nodeCount = 0;
foreach ($newNodes as $n) {
    $nodeStmt->execute($n);
    if ($nodeStmt->rowCount() > 0) $nodeCount++;
}
logResult("Talent Trees: {$treeCount} new trees, +{$nodeCount} nodes (of " . count($newNodes) . " attempted)");


// ═══════════════════════════════════════════════════════════════
// PATCH 5: BONUS FEATS (+5, at non-milestone levels)
// ═══════════════════════════════════════════════════════════════

$newFeats = [
    [3,  'First Conviction', 'Your oath solidifies from words into instinct. You no longer have to think about your values — you embody them automatically.'],
    [7,  'Mentor Bond', 'You form a lasting connection with a mentor figure. Their wisdom becomes part of your operating system. +1 to any secondary attribute.'],
    [12, 'Crucible Survivor', 'You have endured a genuine trial — personal, professional, or spiritual — and emerged stronger. Unlock resilience passive.'],
    [22, 'Cross-Order Insight', 'Deep understanding of an Order other than your own. Can advise, counsel, or collaborate with members of that Order as a peer.'],
    [38, 'Legacy Foundation', 'You lay the formal groundwork for something that will outlive you — an organization, a tradition, a body of work, a family culture.'],
];

$featStmt = $pdo->prepare(
    "INSERT IGNORE INTO `feats` (`level_req`, `name`, `description`) VALUES (?, ?, ?)"
);
$featCount = 0;
foreach ($newFeats as $f) {
    $featStmt->execute($f);
    if ($featStmt->rowCount() > 0) $featCount++;
}
logResult("Feats: +{$featCount} bonus feats (of " . count($newFeats) . " attempted)");


// ═══════════════════════════════════════════════════════════════
// FINAL: VERIFY TOTALS
// ═══════════════════════════════════════════════════════════════

$totals = [];
foreach (['orders','alignments','attributes','callings','specializations','talent_trees','talent_nodes','feats'] as $table) {
    $totals[$table] = (int) $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
}

logResult("FINAL TOTALS — " . implode(' | ', array_map(fn($k,$v) => "$k: $v", array_keys($totals), $totals)), 'info');

$combinatorics = $totals['orders'] * $totals['callings'] * $totals['specializations'] * $totals['alignments'];
logResult("Theoretical build combinations: " . number_format($combinatorics) . " (before attributes and talents)", 'info');


// ═══════════════════════════════════════════════════════════════
// CREATE LOCKFILE — prevents re-execution
// ═══════════════════════════════════════════════════════════════
file_put_contents($lockFile, date('c'));

// ═══════════════════════════════════════════════════════════════
// OUTPUT
// ═══════════════════════════════════════════════════════════════

if ($isWeb) {
    ?><!DOCTYPE html>
<html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>DB Batch 1 — Paladin Profile v3</title>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Source+Sans+3:wght@400;600&family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Source Sans 3',sans-serif;background:#f7f2e6;color:#0f0c08;padding:2rem;max-width:800px;margin:0 auto}
h1{font-family:'Cinzel',serif;color:#8b6410;margin-bottom:1.5rem;text-align:center}
.msg{padding:.6rem 1rem;border-radius:6px;margin-bottom:.4rem;font-size:.88rem;font-family:'JetBrains Mono',monospace}
.msg-ok{background:rgba(26,107,60,.1);border:1px solid rgba(26,107,60,.3);color:#1a3d1e}
.msg-info{background:rgba(200,150,26,.1);border:1px solid rgba(200,150,26,.3);color:#8b6410}
.msg-error{background:rgba(139,20,20,.1);border:1px solid rgba(139,20,20,.3);color:#8b1414}
.back{display:inline-block;margin-top:1.5rem;color:#c8961a;font-weight:600}
.warn{margin-top:1.5rem;padding:1rem;background:rgba(139,20,20,.08);border:1px solid rgba(139,20,20,.2);border-radius:6px;font-size:.85rem;color:#8b1414}
</style>
</head><body>
<h1>⚜ DB Batch 1 Complete</h1>
<?php foreach ($results as $r): ?>
<div class="msg msg-<?= $r['level'] ?>"><?= htmlspecialchars($r['msg']) ?></div>
<?php endforeach; ?>
<a href="/" class="back">← Return to Paladin Profile</a>
<div class="warn"><strong>Security:</strong> Delete this file (<code>patches/batch1.php</code>) after running. It contains database write operations.</div>
</body></html>
<?php
} else {
    // CLI output
    echo "\n═══ PALADIN PROFILE v3 — DB BATCH 1 ═══\n\n";
    foreach ($results as $r) {
        $icon = match($r['level']) { 'ok' => '✓', 'info' => 'ℹ', default => '✗' };
        echo "  $icon {$r['msg']}\n";
    }
    echo "\nDone. Delete patches/batch1.php after running.\n\n";
}
