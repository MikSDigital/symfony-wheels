import React from 'react'
import ArticleListItemWidget from './ArticleListItemWidget';

const ArticleListWidget = (props) => (
    <div>
        {props.articles.map((article, idx) => (
            <div key={idx}>
                <ArticleListItemWidget key={idx} article={article} id={idx}/>
            </div>
        ))}
    </div>
);

export default ArticleListWidget