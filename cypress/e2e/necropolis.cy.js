it('Necropolis', function() {
     cy.visit('http://omekas:8000')
     cy.get('#content [href="/admin"]').click();
     cy.get('[name="email"]').click();
     cy.get('[name="email"]').clear();
     cy.get('[name="email"]').type('admin@example.com');
     cy.get('[name="password"]').click();
     cy.get('[name="password"]').clear();
     cy.get('[name="password"]').type('cypress');
     cy.get('#loginform [name="submit"]').click();
     cy.get('#menu .items').click();
     cy.get('#page-actions .button').click();
     cy.get('#properties [data-value-key="@value"][aria-labelledby="property-1-label"]').click();
     cy.get('#properties [data-value-key="@value"][aria-labelledby="property-1-label"]').type('Item1');
     cy.get('#page-actions [name="add-item-submit"]').click();
     cy.get('#menu .item-sets').click();
     cy.get('#page-actions .button').click();
     cy.get('#properties [data-value-key="@value"][aria-labelledby="property-1-label"]').click();
     cy.get('#properties [data-value-key="@value"][aria-labelledby="property-1-label"]').type('ItemSet1');
     cy.get('#page-actions [name="add-item-set-submit"]').click();
     cy.get('#menu .item-sets').click();
     cy.contains('.resource-name', 'ItemSet1').closest('.tablesaw-cell-content').find('[title="Delete"]').click();
     cy.get('#confirmform [name="submit"]').click();
     cy.get('#menu .necropolis').click();
     cy.contains('.tablesaw-cell-content', 'ItemSet1').click();
     cy.get('#menu [href="/admin/necropolis/item"]').click();
     cy.get('#menu [href="/admin/necropolis/media"]').click();
     cy.get('#menu .items').click();
     cy.contains('.resource-name', 'Item1').closest('.tablesaw-cell-content').find('[title="Delete"]').click();
     cy.get('#confirmform [name="submit"]').click();
     cy.get('#menu .necropolis').click();
     cy.contains('.tablesaw-cell-content', 'ItemSet1').click();
});

