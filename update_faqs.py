import re
from bs4 import BeautifulSoup

html_file = r'd:\updatedtrumarx.github.io\index.html'
with open(html_file, 'r', encoding='utf-8') as f:
    html = f.read()

# We need to construct the new FAQ HTML
faq_list = [
    {
        "q": "How long does trademark registration take in India?",
        "a": "Trademark registration timelines vary depending on examination procedures, objections, and potential opposition from third parties. In a straightforward case where no objections or oppositions arise, the process may take approximately 12 to 18 months. However, once the application is filed, the applicant can begin using the ™ symbol to indicate that trademark protection has been sought."
    },
    {
        "q": "What can be registered as a trademark?",
        "a": "A trademark may include any distinctive sign capable of identifying the source of goods or services. Examples include:<br>• Brand names<br>• Logos and symbols<br>• Taglines or slogans<br>• Product packaging (trade dress)<br>• Labels and device marks<br>The mark must be distinctive and not deceptively similar to existing registered trademarks."
    },
    {
        "q": "Can trademarks be registered internationally?",
        "a": "Yes. Businesses can extend their trademark protection internationally through systems such as the Madrid Protocol, which allows trademark owners to file a single international application covering multiple member countries. International trademark strategy typically depends on:<br>• Target markets<br>• Business expansion plans<br>• Existing brand usage<br>• Jurisdiction-specific legal requirements<br>Our team assists clients in coordinating international trademark filings through trusted global partners."
    },
    {
        "q": "What happens if someone uses my registered trademark without permission?",
        "a": "Unauthorized use of a registered trademark may constitute trademark infringement. The trademark owner may take legal steps such as:<br>• Issuing a cease-and-desist notice<br>• Initiating opposition or cancellation proceedings<br>• Filing civil infringement actions before the appropriate courts<br>Timely legal action helps protect brand reputation and prevent dilution of the trademark."
    },
    {
        "q": "What is a Trademark Watch service and why is it important?",
        "a": "A Trademark Watch service monitors newly filed trademark applications and market activity to identify marks that may conflict with your registered or pending trademarks. This helps businesses:<br>• Detect similar or conflicting trademarks early<br>• Oppose problematic applications within statutory timelines<br>• Protect long-term brand value<br>Trademark watch services are particularly important for growing brands and companies operating across multiple markets."
    },
    {
        "q": "Do startups and small businesses also need intellectual property protection?",
        "a": "Yes. Intellectual property protection is often most critical during the early stages of a business. Protecting brand names, logos, technology, and creative assets helps startups:<br>• Prevent brand duplication<br>• Build investor confidence<br>• Establish long-term commercial value<br>• Safeguard innovation<br>Early-stage IP protection can significantly reduce legal risks as the business scales."
    },
    {
        "q": "What industries does Trumarx work with?",
        "a": "Trumarx works with clients across a diverse range of industries including:<br>• Technology and software<br>• Consumer goods and retail<br>• Manufacturing and industrial products<br>• Hospitality and entertainment<br>• Media and creative industries<br>• Startups and emerging businesses<br>Our approach focuses on understanding the commercial and strategic needs of each sector."
    },
    {
        "q": "Can intellectual property be licensed or sold?",
        "a": "Yes. Intellectual property rights can be licensed, assigned, or commercially transferred. Businesses often monetize their intellectual property through:<br>• Licensing agreements<br>• Franchising models<br>• Brand partnerships<br>• Technology transfer agreements<br>Structured IP agreements help create additional revenue streams while protecting ownership rights."
    },
    {
        "q": "What is the difference between ™ and ®?",
        "a": "• ™ (Trademark) indicates that a mark is being used as a trademark but may not yet be registered.<br>• ® (Registered Trademark) indicates that the trademark has been officially registered with the trademark registry.<br>The ® symbol can only be used after registration is granted."
    },
    {
        "q": "Why is intellectual property important for growing businesses?",
        "a": "Intellectual property protection helps businesses:<br>• Secure brand ownership<br>• Protect innovation and creativity<br>• Prevent unauthorized use by competitors<br>• Enhance company valuation<br>• Strengthen investor and market confidence<br>A well-managed IP portfolio becomes a valuable strategic asset for long-term business growth."
    },
    {
        "q": "When should a business start protecting its intellectual property?",
        "a": "Ideally, intellectual property protection should begin at the earliest stage of brand or product development. Businesses should consider trademark, design, or patent protection before launching a brand, product, or service in the market. Early protection helps avoid conflicts, prevents brand duplication, and secures ownership rights from the outset. Delaying IP protection can increase the risk of third parties registering similar or identical intellectual assets."
    },
    {
        "q": "Can intellectual property protection increase the value of a business?",
        "a": "Yes. Intellectual property is often considered one of the most valuable intangible assets of a company. Well-protected IP portfolios can:<br>• Increase company valuation<br>• Strengthen investor confidence<br>• Support licensing and franchising opportunities<br>• Create additional revenue streams<br>• Enhance brand credibility in domestic and international markets<br>Many successful global companies derive a significant portion of their value from their intellectual property assets."
    },
    {
        "q": "How does Trumarx assist businesses with long-term IP strategy?",
        "a": "Beyond individual filings, Trumarx assists clients in building structured intellectual property strategies aligned with their business growth plans. Our advisory approach includes:<br>• Trademark portfolio development<br>• International expansion planning<br>• Trademark watch and monitoring<br>• Infringement risk assessment<br>• Licensing and commercialization strategy<br>This ensures that intellectual property protection supports both legal security and long-term business value."
    }
]

retained_faqs = [
    {
        "q": "Do you handle international patent filing?",
        "a": "Yes. We specialize in PCT (Patent Cooperation Treaty) applications, allowing you to seek patent protection in over 150 countries simultaneously."
    },
    {
        "q": "Can I trademark a name before I start my business?",
        "a": "Yes! You can file on a \"Proposed to be Used\" basis. This secures your priority date and protects your brand name before you even launch."
    },
    {
        "q": "How is Trumarx different from other firms?",
        "a": "We don't just file paperwork; we provide strategic IP counsel. With over 20 years of experience and a 98% success rate, we actively monitor and protect your brand asset, acting as your long-term legal partner."
    }
]

# We will generate a single large string representing the inner HTML of the faq container
new_items_html = ""

template = """                <details class="group bg-white p-6 rounded-2xl shadow-sm cursor-pointer">
                    <summary class="flex justify-between items-center font-bold text-black">
                        {question}
                        <span class="transform group-open:rotate-180 transition-transform duration-300"><i
                                class="fas fa-chevron-down text-brand-accent"></i></span>
                    </summary>
                    <div class="faq-answer">
                        <div>
                            <p class="text-gray-600 pt-4 text-sm leading-relaxed">
                                {answer}
                            </p>
                        </div>
                    </div>
                </details>
"""

# add the 13 FAQs first
for faq in faq_list:
    new_items_html += template.format(question=faq["q"], answer=faq["a"])

# add retained FAQs
new_items_html += "\n                <!-- Retained Original FAQs -->\n"
for faq in retained_faqs:
    new_items_html += template.format(question=faq["q"], answer=faq["a"])


# find the block to replace
start_comment = '<div class="space-y-4">'
end_tag = '</div>\n        </div>\n    </section>\n\n    <!-- Footer -->'

idx1 = html.find(start_comment)
idx2 = html.find(end_tag, idx1)

if idx1 == -1 or idx2 == -1:
    print("Could not find the bounds for FAQ section")
    exit(1)

# Only replace inside div.space-y-4
replacement = '<div class="space-y-4">\n' + new_items_html + '            ' + end_tag
new_html = html[:idx1] + replacement + html[idx2+len(end_tag):]

with open(html_file, 'w', encoding='utf-8') as f:
    f.write(new_html)

print("FAQS UPDATED SUCCESSFULLY")
